<?php
declare(strict_types = 1);

namespace Simbiat\Website\bictracker;

use Simbiat\ArrayHelpers;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Security;

use function count, in_array;

/**
 * Class to process BIC library
 */
class Library
{
    private string $fileDate;
    #Base link where we download BIC files
    public const string bicDownBase = 'https://www.cbr.ru/PSystem/payment_system/?UniDbQuery.Posted=True&UniDbQuery.To=';
    #Base link for href attribute
    public const string bicBaseHref = 'https://www.cbr.ru';
    #Queries to process
    private array $queries = [];
    
    /**
     * Function to update library in database
     */
    public function update(bool $manual = false): string|bool|int
    {
        if (empty(Config::$dbController)) {
            return false;
        }
        $currentDate = strtotime(date('d.m.Y'));
        #Get date of current library
        $libDate = $this->bicDate();
        $libDateInitial = strtotime(date('d.m.Y', (int)$libDate));
        $libDate = $libDateInitial;
        while ($libDate <= $currentDate) {
            try {
                $download = $this->download($libDate);
                if ($download === true) {
                    #The day does not have library, skip it
                    $this->log($libDate, 'Библиотека за день не найдена: день пропущен', $manual);
                    $libDate += 86400;
                    continue;
                }
                if ($download === false) {
                    #If date is current one, then assume that file is simply not available yet
                    if ($libDate >= $currentDate) {
                        $this->log($libDate, 'Библиотека за день не найдена: скорее всего, ещё не опубликована', $manual);
                        return true;
                    }
                    #Failed to download. Stop processing to avoid loosing sequence
                    throw new \RuntimeException('Не удалось скачать файл');
                }
                #Some files are known to have double XML definition. We need to fix this.
                file_put_contents($download, preg_replace('/(<\?xml version="1\.0" encoding="WINDOWS-1251"\?>){2,}/i', '$1', file_get_contents($download)));
                #Load file
                $library = new \DOMDocument();
                $loadSuccess = $library->load(realpath($download), LIBXML_PARSEHUGE | LIBXML_COMPACT | LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
                if ($loadSuccess === false) {
                    #Bad file detected
                    throw new \DOMException('Не удалось открыть файл `'.$download.'`');
                }
                #Some files are in packets or envelopes, thus we need to explicitly get ED807 element and work with it.
                $library = $library->getElementsByTagName('ED807')->item(0);
                #Get date from root node. Earlier libraries did not have BusinessDay, but later it was added, because it became possible for the library to be prepared before the day, when it needed applying.
                #Using @ to suppress potential errors and also allow ?: instead of ??, since ?? will treat empty string as valid
                $this->fileDate = $library->getAttribute('BusinessDay') ?: $library->getAttribute('EDDate');
                #Check date of the library
                if (empty($this->fileDate)) {
                    #Empty date. Stop processing to avoid loosing sequence
                    throw new \LengthException('Не удалось получить дату из файла `'.$download.'`');
                }
                if ($this->fileDate !== date('Y-m-d', $libDate)) {
                    #Date mismatch. Stop processing to avoid loosing sequence
                    throw new \UnexpectedValueException('Дата в файле не совпадает с ожидаемой `'.$download.'`');
                }
                #Get entries
                $elements = $library->getElementsByTagName('BICDirectoryEntry');
                #List of BICs to compare against current database
                $bics = [];
                #List of BICs to add to be used later
                $delayed = [];
                #Iterate entries
                foreach ($elements as $element) {
                    #Flag determining whether we delay or not
                    $delay = false;
                    $this->queries = [];
                    #Get BIC
                    $bic = $element->getAttribute('BIC');
                    $bics[] = $bic;
                    #Get general details
                    $details = $element->getElementsByTagName('ParticipantInfo')[0];
                    #Get restrictions
                    $restrictions = $element->getElementsByTagName('RstrList');
                    #Get SWIFT codes
                    $swifts = $element->getElementsByTagName('SWBICS');
                    #Get accounts
                    $accounts = $element->getElementsByTagName('Accounts');
                    #Generate array, which can be compared to what we can get from DB
                    $details = ArrayHelpers::attributesToArray($details, true, ['BIC', 'DateIn', 'DateOut', 'NameP', 'EnglName', 'XchType', 'PtType', 'Srvcs', 'UID', 'PrntBIC', 'CntrCd', 'RegN', 'Ind', 'Rgn', 'Tnp', 'Nnp', 'Adr']);
                    $details['BIC'] = $bic;
                    #Ensure some old or unused fields are removed
                    unset($details['NPSParticipant'], $details['ParticipantStatus']);
                    ksort($details, SORT_NATURAL);
                    #Prepare bindings
                    $bindings = [];
                    foreach (array_keys($details) as $key) {
                        $bindings[':'.$key] = [
                            $details[$key],
                            ($details[$key] === NULL ? 'null' : 'string'),
                        ];
                    }
                    #Get current details
                    $currentDetails = $this->getBIC($bic);
                    #Check for Parent BIC
                    if (!empty($details['PrntBIC']) && empty($this->getBIC($details['PrntBIC']))) {
                        $delay = true;
                    }
                    #Check if BIC exists at all
                    if (empty($currentDetails)) {
                        #We need to INSERT
                        $this->queries[] = [
                            'INSERT INTO `bic__list` (`BIC`, `DateIn`, `DateOut`, `Updated`, `NameP`, `EnglName`, `XchType`, `PtType`, `Srvcs`, `UID`, `PrntBIC`, `CntrCd`, `RegN`, `Ind`, `Rgn`, `Tnp`, `Nnp`, `Adr`) VALUES (:BIC, :DateIn, :DateOut, :fileDate, :NameP, :EnglName, :XchType, :PtType, :Srvcs, :UID, :PrntBIC, :CntrCd, :RegN, :Ind, :Rgn, :Tnp, :Nnp, :Adr);',
                            array_merge($bindings, [':fileDate' => $this->fileDate]),
                        ];
                    } elseif ($details !== $currentDetails) {
                        #Compare details, if they are different - we need to update
                        $this->queries[] = [
                            'UPDATE `bic__list` SET `DateIn`=:DateIn, `DateOut`=:DateOut, `Updated`=:fileDate, `NameP`=:NameP, `EnglName`=:EnglName, `XchType`=:XchType, `PtType`=:PtType, `Srvcs`=:Srvcs, `UID`=:UID, `PrntBIC`=:PrntBIC, `CntrCd`=:CntrCd, `RegN`=:RegN, `Ind`=:Ind, `Rgn`=:Rgn, `Tnp`=:Tnp, `Nnp`=:Nnp, `Adr`=:Adr WHERE `BIC`=:BIC;',
                            array_merge($bindings, [':fileDate' => $this->fileDate]),
                        ];
                    }
                    #Process restrictions
                    if (count($restrictions) > 0) {
                        #Convert to array
                        $libraryRest = [];
                        foreach ($restrictions as $restriction) {
                            $libraryRest[] = ArrayHelpers::attributesToArray($restriction);
                            ksort($libraryRest[array_key_last($libraryRest)]);
                        }
                        #Get current restrictions
                        $currentRest = $this->getRestrictions($bic);
                        #Check if any of restrictions were removed
                        foreach ($currentRest as $restriction) {
                            if (!in_array($restriction, $libraryRest, true)) {
                                #Update DateOut for restriction
                                $this->queries[] = $this->endRestriction($bic, $restriction);
                            }
                        }
                        #Add new restrictions
                        foreach ($libraryRest as $restriction) {
                            if (!in_array($restriction, $currentRest, true)) {
                                #Insert restriction
                                $this->queries[] = [
                                    'INSERT IGNORE INTO `bic__bic_rstr` (`BIC`, `Rstr`, `RstrDate`) VALUES (:BIC, :Rstr, :RstrDate);',
                                    [
                                        ':BIC' => $bic,
                                        ':Rstr' => $restriction['Rstr'],
                                        ':RstrDate' => $restriction['RstrDate'],
                                    ]
                                ];
                            }
                        }
                    } else {
                        #End all restrictions if any exist
                        $this->queries[] = $this->endRestriction($bic);
                    }
                    #Process swifts
                    if (count($swifts) > 0) {
                        #Convert to array
                        $librarySwift = [];
                        foreach ($swifts as $swift) {
                            $librarySwift[] = ArrayHelpers::attributesToArray($swift);
                            ksort($librarySwift[array_key_last($librarySwift)]);
                        }
                        #Get current SWIFTs
                        $currentSwift = $this->getSWIFTs($bic);
                        #Add all SWIFTs. Updating Default flag if already existing
                        foreach ($librarySwift as $swift) {
                            if (!in_array($swift, $currentSwift, true)) {
                                #Insert restriction
                                $this->queries[] = [
                                    'INSERT INTO `bic__swift` (`BIC`, `SWBIC`, `DefaultSWBIC`, `DateIn`) VALUES (:BIC, :SWBIC, :DefaultSWBIC, :fileDate) ON DUPLICATE KEY UPDATE `DefaultSWBIC`=:DefaultSWBIC;',
                                    [
                                        ':BIC' => $bic,
                                        ':SWBIC' => $swift['SWBIC'],
                                        ':DefaultSWBIC' => $swift['DefaultSWBIC'],
                                        ':fileDate' => $this->fileDate,
                                    ]
                                ];
                            }
                        }
                        #Close SWIFTs that do not match what we already have. If Default flag has been updated on previous step, there will be no update here, because it will no longer match the condition
                        foreach ($currentSwift as $swift) {
                            if (!in_array($swift, $librarySwift, true)) {
                                #Close SWIFT
                                $this->queries[] = $this->closeSwift($bic, $swift['SWBIC'], $swift['DefaultSWBIC']);
                            }
                        }
                    } else {
                        #Close all SWIFTs
                        $this->queries[] = $this->closeSwift($bic);
                    }
                    #Process accounts
                    if (count($accounts) > 0) {
                        #Convert to array
                        $libraryAccounts = [];
                        $libraryAccountsRest = [];
                        foreach ($accounts as $account) {
                            #Convert account
                            $libraryAccounts[] = ArrayHelpers::attributesToArray($account, true, ['CK']);
                            #Set last key
                            $lastKey = array_key_last($libraryAccounts);
                            unset($libraryAccounts[$lastKey]['AccountStatus']);
                            ksort($libraryAccounts[$lastKey]);
                            #Convert restrictions
                            if (count($account->getElementsByTagName('AccRstrList')) > 0) {
                                foreach ($account->getElementsByTagName('AccRstrList') as $restriction) {
                                    $libraryAccountsRest[$libraryAccounts[$lastKey]['Account']][] = ArrayHelpers::attributesToArray($restriction, true, ['SuccessorBIC']);
                                    ksort($libraryAccountsRest[$libraryAccounts[$lastKey]['Account']]);
                                }
                            }
                        }
                        #Get current accounts
                        $currentAccounts = $this->getAccounts($bic);
                        #"Remove" accounts
                        foreach ($currentAccounts as $account) {
                            if (!in_array($account, $libraryAccounts, true)) {
                                $this->closeAccount($bic, $account['Account']);
                            }
                        }
                        #Update accounts
                        foreach ($libraryAccounts as $account) {
                            if (!empty($account['AccountCBRBIC']) && empty($this->getBIC($account['AccountCBRBIC']))) {
                                $delay = true;
                            }
                            #Update account
                            $this->queries[] = [
                                'INSERT INTO `bic__accounts` (`BIC`, `Account`, `AccountCBRBIC`, `RegulationAccountType`, `CK`, `DateIn`) VALUES (:BIC, :Account, :AccountCBRBIC, :RegulationAccountType, :CK, :DateIn) ON DUPLICATE KEY UPDATE `AccountCBRBIC`=:AccountCBRBIC, `RegulationAccountType`=:RegulationAccountType, `CK`=:CK, `DateIn`=:DateIn, `DateOut`=NULL;',
                                [
                                    ':BIC' => $bic,
                                    ':Account' => $account['Account'],
                                    #There are known cases, when BIC was set to '000000000' for some reason, thus we need to replace it with NULL. We also cover possibility, that it will not be present at all.
                                    ':AccountCBRBIC' => [
                                        (empty((int)$account['AccountCBRBIC']) ? NULL : $account['AccountCBRBIC']),
                                        (empty((int)$account['AccountCBRBIC']) ? 'null' : 'string'),
                                    ],
                                    ':RegulationAccountType' => $account['RegulationAccountType'],
                                    ':CK' => $account['CK'],
                                    ':DateIn' => $account['DateIn'],
                                ]
                            ];
                            if (!empty($libraryAccountsRest[$account['Account']])) {
                                #Get current restrictions
                                $currentRest = $this->getAccountRestrictions($account['Account']);
                                #Add all new restrictions
                                foreach ($libraryAccountsRest[$account['Account']] as $restriction) {
                                    if (!in_array($restriction, $currentRest, true)) {
                                        if (!empty($restriction['SuccessorBIC']) && empty($this->getBIC($restriction['SuccessorBIC']))) {
                                            $delay = true;
                                        }
                                        #Insert restriction
                                        $this->queries[] = [
                                            'INSERT INTO `bic__acc_rstr` (`Account`, `AccRstr`, `AccRstrDate`, `SuccessorBIC`) VALUES (:Account, :AccRstr, :AccRstrDate, :SuccessorBIC) ON DUPLICATE KEY UPDATE `SuccessorBIC`=:SuccessorBIC;',
                                            [
                                                ':Account' => $account['Account'],
                                                ':AccRstr' => $restriction['AccRstr'],
                                                ':AccRstrDate' => $restriction['AccRstrDate'],
                                                ':SuccessorBIC' => $restriction['SuccessorBIC'],
                                            ]
                                        ];
                                    }
                                }
                                #Check if any of restrictions were removed
                                foreach ($currentRest as $restriction) {
                                    if (!in_array($restriction, $libraryAccountsRest[$account['Account']], true)) {
                                        #End restriction
                                        $this->queries[] = $this->endAccountRestriction($account['Account'], true, ['AccRstr' => $restriction['AccRstr'], 'AccRstrDate' => $restriction['AccRstrDate'],]);
                                    }
                                }
                            } else {
                                #End all restrictions for the account
                                $this->queries[] = $this->endAccountRestriction($account['Account'], true);
                            }
                        }
                    } else {
                        #Close all accounts
                        $this->closeAccount($bic);
                    }
                    #If flag is true, it means that there is a dependency on a BIC, which is not yet present, thus we need to add run the queries after BIC is added, but since we can't predict when it will be added, we do this outside of the loop.
                    if ($delay) {
                        $delayed = array_merge($delayed, $this->queries);
                    } elseif (Config::$dbController->query($this->queries) !== true) {
                        #Appply queries for this BIC
                        throw new \RuntimeException('Failed to update `'.$bic.'` from `'.$download.'`');
                    }
                }
                #Replace list of queries with delayed queries
                $this->queries = $delayed;
                #Check for removed BICs
                $currentBics = $this->getBICs();
                foreach ($currentBics as $bic) {
                    if (!in_array($bic, $bics, true)) {
                        #Close bic
                        $this->closeBIC($bic);
                    }
                }
                #Reset Default flag for SWIFTs with DateOut (the way data is presented by CB, it's possible for them to have incorrect flag).
                $this->queries[] = ['UPDATE `bic__swift` SET `DefaultSWBIC`=0 WHERE `DateOut` IS NOT NULL;'];
                #Set `DateIn` for "bad" entries. We are assuming, that affected entries were added at least at the time of BIC library creation. Another case of "bad" data.
                $this->queries[] = ['UPDATE `bic__list` SET `DateIn`=\'1996-07-10\' WHERE `DateIn` IS NULL OR `DateIn`=\'1970-01-01\';'];
                $this->queries[] = ['UPDATE `bic__accounts` SET `DateIn`=\'1996-07-10\' WHERE `DateIn`=\'1970-01-01\';'];
                $this->queries[] = [
                    'UPDATE `bic__settings` SET `value`=:date WHERE `setting`=\'date\';',
                    [':date' => $libDate],
                ];
                #Run queries for BICs removals and library update
                Config::$dbController->query($this->queries);
                $this->log($libDate, 'Успешное обновление', $manual);
                if ($manual && $libDate !== $libDateInitial) {
                    return $libDate;
                }
                #Increase $libDate by 1 day
                $libDate += 86400;
            } catch (\Exception $e) {
                $error = $e->getMessage()."\r\n".$e->getTraceAsString();
                $this->log($libDate, $error, $manual);
                return $error;
            } finally {
                #Remove all library related files if any were identified
                array_map('unlink', glob(sys_get_temp_dir().'/*_ED807_full.*'));
            }
        }
        return true;
    }
    
    #############################
    #Helper functions to get data
    #############################
    /**
     * Get a BIC from DB
     */
    private function getBIC(string $bic): array
    {
        $result = Config::$dbController->selectRow(
            'SELECT `BIC`, `DateIn`, `DateOut`, `NameP`, `EnglName`, `XchType`, `PtType`, `Srvcs`, `UID`, `PrntBIC`, `CntrCd`, `RegN`, `Ind`, `Rgn`, `Tnp`, `Nnp`, `Adr` FROM `bic__list` WHERE `BIC`=:BIC;',
            [':BIC' => $bic,]
        );
        if (!empty($result)) {
            ksort($result, SORT_NATURAL);
            #Pad BICs with zeros
            $result['BIC'] = mb_str_pad((string)$result['BIC'], 9, '0', STR_PAD_LEFT, 'UTF-8');
            if ($result['PrntBIC'] !== NULL) {
                $result['PrntBIC'] = mb_str_pad((string)$result['PrntBIC'], 9, '0', STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get restrictions
     */
    private function getRestrictions(string $bic): array
    {
        return Config::$dbController->selectAll(
            'SELECT `Rstr`, `RstrDate` FROM `bic__bic_rstr` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,]
        );
    }
    
    /**
     * Get SWIFT accounts
     */
    private function getSWIFTs(string $bic): array
    {
        return Config::$dbController->selectAll(
            'SELECT `DefaultSWBIC`, `SWBIC` FROM `bic__swift` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,]
        );
    }
    
    /**
     * #Get accounts
     */
    private function getAccounts(string $bic): array
    {
        $result = Config::$dbController->selectAll(
            'SELECT `Account`, `AccountCBRBIC`, `CK`, `DateIn`, `RegulationAccountType` FROM `bic__accounts` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,]
        );
        #Pad BICs with zeros
        foreach ($result as $key => $account) {
            if ($account['AccountCBRBIC'] !== NULL) {
                $result[$key]['AccountCBRBIC'] = mb_str_pad((string)$account['AccountCBRBIC'], 9, '0', STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get account restrictions
     */
    private function getAccountRestrictions(string $account): array
    {
        $result = Config::$dbController->selectAll(
            'SELECT `AccRstr`, `AccRstrDate`, `SuccessorBIC` FROM `bic__acc_rstr` WHERE `Account`=:Account;',
            [':Account' => $account,]
        );
        foreach ($result as $key => $restriction) {
            if ($restriction['SuccessorBIC'] !== NULL) {
                $result[$key]['SuccessorBIC'] = mb_str_pad((string)$restriction['SuccessorBIC'], 9, '0', STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get all BICs
     */
    private function getBICs(): array
    {
        return Config::$dbController->selectColumn('SELECT `BIC` FROM `bic__list` WHERE `DateOut` IS NULL;');
    }
    
    ###################################
    #Helper functions to close entities
    ###################################
    
    /**
     * Close BIC
     * @param string $bic
     *
     * @return void
     */
    private function closeBIC(string $bic): void
    {
        #Set end of restriction for all entries if any exist
        $this->queries[] = $this->endRestriction($bic);
        #Close all SWIFTs
        $this->queries[] = $this->closeSwift($bic);
        #Close all accounts
        $this->closeAccount($bic);
        #Close BIC itself
        $this->queries[] = [
            'UPDATE `bic__list` SET `DateOut`=:fileDate WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [
                ':BIC' => $bic,
                ':fileDate' => $this->fileDate,
            ]
        ];
    }
    
    /**
     * End a restriction
     * @param string     $bic         BIC we are working with
     * @param array|null $restriction Restriction(s) to close. If `null`, will close all restrictions.
     *
     * @return array
     */
    private function endRestriction(string $bic, ?array $restriction = NULL): array
    {
        if (empty($bic)) {
            return [];
        }
        #If no details - assume we are ending all restrictions
        if (empty($restriction)) {
            return [
                'UPDATE `bic__bic_rstr` SET `DateOut`=:fileDate WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':fileDate' => $this->fileDate,
                ]
            ];
        }
        #Otherwise, use details to narrow down
        if (!isset($restriction['Rstr'], $restriction['RstrDate'])) {
            return [];
        }
        return [
            'UPDATE `bic__bic_rstr` SET `DateOut`=:fileDate WHERE `BIC`=:BIC AND `Rstr`=:Rstr AND `RstrDate`=:RstrDate;',
            [
                ':BIC' => $bic,
                ':Rstr' => $restriction['Rstr'],
                ':RstrDate' => $restriction['RstrDate'],
                ':fileDate' => $this->fileDate,
            ]
        ];
    }
    
    /**
     * Close SWIFT
     * @param string          $bic     BIC we are working with
     * @param string|null     $swift   SWIFT to close
     * @param string|int|bool $default Whether this is a default BIC or not
     *
     * @return array
     */
    private function closeSwift(string $bic, ?string $swift = NULL, string|int|bool $default = false): array
    {
        if (empty($bic)) {
            return [];
        }
        #If swift is empty, assume, that we are removing all accounts
        if (empty($swift)) {
            return [
                'UPDATE `bic__swift` SET `DateOut`=:fileDate, `DefaultSWBIC`=0 WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':fileDate' => $this->fileDate,
                ]
            ];
        }
        return [
            'UPDATE `bic__swift` SET `DateOut`=:fileDate, `DefaultSWBIC`=0 WHERE `BIC`=:BIC AND `SWBIC`=:SWBIC AND `DefaultSWBIC`=:DefaultSWBIC;',
            [
                ':BIC' => $bic,
                ':SWBIC' => $swift,
                ':DefaultSWBIC' => [$default, 'bool'],
                ':fileDate' => $this->fileDate,
            ]
        ];
    }
    
    /**
     * Close account(s)
     * @param string      $bic     BIC we are working with
     * @param string|null $account Account to close. If `null`, all accounts will be closed.
     *
     * @return void
     */
    private function closeAccount(string $bic, ?string $account = NULL): void
    {
        #If account is empty, assume, that we are removing all accounts
        if (empty($account)) {
            #End restrictions
            $this->queries[] = $this->endAccountRestriction($bic);
            #Close all open accounts
            $this->queries[] = [
                'UPDATE `bic__accounts` SET `DateOut`=:fileDate WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':fileDate' => $this->fileDate,
                ]
            ];
        } else {
            #End restrictions
            $this->queries[] = $this->endAccountRestriction($account, true);
            #Close account
            $this->queries[] = [
                'UPDATE `bic__accounts` SET `DateOut`=:fileDate WHERE `BIC`=:BIC AND `Account`=:Account AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':Account' => $account,
                    ':fileDate' => $this->fileDate,
                ]
            ];
        }
    }
    
    /**
     * End account restrictions
     * @param string     $bic         BIC we are working with
     * @param bool       $account     Account we are working with
     * @param array|null $restriction Restriction(s) to close. If `null`, will close all restrictions
     *
     * @return array
     */
    private function endAccountRestriction(string $bic, bool $account = false, ?array $restriction = NULL): array
    {
        if (empty($bic)) {
            return [];
        }
        #If account flag is true - we know account
        if ($account) {
            #If no details - end all restrictions
            if (empty($restriction)) {
                return [
                    'UPDATE `bic__acc_rstr` SET `DateOut`=:fileDate WHERE `Account`=:Account AND `DateOut` IS NULL;',
                    [
                        ':Account' => $bic,
                        ':fileDate' => $this->fileDate,
                    ]
                ];
            }
            #Otherwise, use details to narrow down
            if (!isset($restriction['AccRstr'], $restriction['AccRstrDate'])) {
                return [];
            }
            return [
                'UPDATE `bic__acc_rstr` SET `DateOut`=:fileDate WHERE `Account`=:Account AND `AccRstr`=:AccRstr AND `AccRstrDate`=:AccRstrDate;',
                [
                    ':Account' => $bic,
                    ':AccRstr' => $restriction['AccRstr'],
                    ':AccRstrDate' => $restriction['AccRstrDate'],
                    ':fileDate' => $this->fileDate,
                ]
            ];
        }
        #Otherwise, we are removing everything for whole BIC
        return [
            'UPDATE `bic__acc_rstr` SET `DateOut`=:fileDate WHERE `DateOut` IS NULL AND `Account` IN (SELECT `Account` FROM `bic__accounts` WHERE `BIC`=:BIC AND `DateOut` IS NULL);',
            [
                ':BIC' => $bic,
                ':fileDate' => $this->fileDate,
            ]
        ];
    }
    
    /**
     * Function to log updates
     * @param      $bicdate
     * @param      $message
     * @param bool $manual
     *
     * @return void
     */
    private function log($bicdate, $message, bool $manual = false): void
    {
        Security::log('BIC Tracker', ($manual ? 'Manual' : 'Cron').' update', $message.' ('.date('d.m.Y', $bicdate).')', ($manual === false ? Config::userIDs['System user'] : null));
    }
    
    /**
     * Function to download BIC
     */
    public function download(int $date): bool|string
    {
        #Generate zip path
        $fileName = sys_get_temp_dir().'/'.date('Ymd', $date).'_ED807_full.xml';
        #Generate link
        $link = self::bicDownBase.date('d.m.Y', $date);
        $data = (new Curl())->getPage($link);
        if (!is_string($data)) {
            return false;
        }
        #Load page as DOM Document
        libxml_use_internal_errors(true);
        $page = new \DOMDocument();
        $page->loadHTML($data);
        #Get all links on page
        $as = $page->getElementsByTagName('a');
        #Iterrate links to find the one we need
        foreach ($as as $a) {
            #Filter only those that has proper value
            if (preg_match('/\s*Справочник БИК\s*/iu', $a->textContent) === 1) {
                #Get href attribute
                $href = $a->getAttribute('href');
                #Skip a link for "current" library
                if (preg_match('/\/s\/newbik/iu', $href) === 0) {
                    $href = self::bicBaseHref.$href;
                    #Attempt to actually download the zip file
                    $bicFile = (new Curl())->getFile($href);
                    if (\is_array($bicFile) && !empty($bicFile['server_name'])) {
                        $bicFile = $bicFile['server_path'].'/'.$bicFile['server_name'];
                    } else {
                        return false;
                    }
                    #Unzip the file
                    if (file_exists($bicFile)) {
                        $zip = new \ZipArchive();
                        if ($zip->open($bicFile) === true) {
                            $zip->extractTo(sys_get_temp_dir());
                            $zip->close();
                        }
                        #Remove zip file
                        /** @noinspection PhpUsageOfSilenceOperatorInspection */
                        @unlink($bicFile);
                        #Check if ED807 file exists
                        if (file_exists($fileName)) {
                            return $fileName;
                        }
                        return false;
                    }
                    return true;
                }
            }
        }
        #This means, that no file was found for the date (which is not necessarily a problem)
        return true;
    }
    
    /**
     * Function to get current library date
     * @return string
     */
    public function bicDate(): string
    {
        try {
            return Config::$dbController->selectValue('SELECT `value` FROM `bic__settings` WHERE `setting`=\'date\';');
        } catch (\Throwable) {
            return (string)time();
        }
    }
}

<?php
declare(strict_types = 1);

namespace App\Service;

use App\Enum\LogType;
use App\Enum\SystemUser;
use App\Security\Security;
use Simbiat\ArrayHelpers\Converters;
use Simbiat\Database\Query;
use Simbiat\StringHelpers\Sanitize;
use function count;
use function in_array;

/**
 * Class to process the BIC library
 */
class BICLibrary
{
    /**
     * Date from the XML file
     */
    private string $file_date;
    /**
     * Base link where we download BIC files
     */
    public const string BIC_DOWN_BASE = 'https://www.cbr.ru/PSystem/payment_system/?UniDbQuery.Posted=True&UniDbQuery.To=';
    /**
     * Base link for href attribute
     */
    public const string BIC_BASE_HREF = 'https://www.cbr.ru';
    /**
     * Queries to process
     */
    private array $queries = [];
    
    /**
     * Function to update the library in the database
     */
    public function update(bool $manual = false): string|bool|int
    {
        $current_date = \DateTime::createFromTimestamp(\time());
        #Get the date of the current library
        $lib_date = $this->bicDate();
        $lib_date_initial = $lib_date->format('Y-m-d');
        while ($lib_date->format('Y-m-d') <= $current_date->format('Y-m-d')) {
            try {
                $download = $this->download($lib_date);
                if ($download === true) {
                    #The day does not have a library, skip it
                    $this->log($lib_date->format('d.m.Y'), 'Библиотека за день не найдена: день пропущен', $manual);
                    $lib_date->add(new \DateInterval('P1D'));
                    continue;
                }
                if ($download === false) {
                    #If date is current one or somehow from the future, then assume that file is simply not available yet
                    if ($lib_date->format('Y-m-d') >= $current_date->format('Y-m-d')) {
                        $this->log($lib_date->format('d.m.Y'), 'Библиотека за день не найдена: скорее всего, ещё не опубликована', $manual);
                        return true;
                    }
                    #Failed to download. Stop processing to avoid losing the sequence
                    throw new \RuntimeException('Не удалось скачать файл');
                }
                #Some files are known to have double XML definition. We need to fix this.
                \file_get_contents($download)
                    |> (static fn($x) => \preg_replace('/(<\?xml version="1\.0" encoding="WINDOWS-1251"\?>){2,}/i', '$1', $x))
                    |> (static fn($x) => \file_put_contents($download, $x));
                #Load file
                $library = new \DOMDocument();
                if (!$library->load(\realpath($download), \LIBXML_PARSEHUGE | \LIBXML_COMPACT | \LIBXML_NOWARNING | \LIBXML_NOERROR | \LIBXML_NONET)) {
                    #Bad file detected
                    throw new \DOMException('Не удалось открыть файл `'.$download.'`');
                }
                #Some files are in packets or envelopes, thus we need to explicitly get an ED807 element and work with it.
                $library = $library->getElementsByTagName('ED807')->item(0);
                #Get the date from the root node. Earlier libraries did not have BusinessDay, but later it was added because it became possible for the library to be prepared before the day, when it needed applying.
                #Using @ to suppress potential errors and also allow `?:` instead of `??`, because `??` will treat an empty string as valid
                $this->file_date = $library->getAttribute('BusinessDay') ?: $library->getAttribute('EDDate');
                #Check library date
                if (Sanitize::whiteString($this->file_date)) {
                    #Empty date. Stop processing to avoid losing the sequence
                    throw new \LengthException('Не удалось получить дату из файла `'.$download.'`');
                }
                if ($this->file_date !== $lib_date->format('Y-m-d')) {
                    #Date mismatch. Stop processing to avoid losing the sequence
                    throw new \UnexpectedValueException('Дата в файле не совпадает с ожидаемой `'.$download.'`');
                }
                #Get entries
                $elements = $library->getElementsByTagName('BICDirectoryEntry');
                #List of BICs to compare against the current database
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
                    $details = Converters::attributesToArray($details, true, ['BIC', 'DateIn', 'DateOut', 'NameP', 'EnglName', 'XchType', 'PtType', 'Srvcs', 'UID', 'PrntBIC', 'CntrCd', 'RegN', 'Ind', 'Rgn', 'Tnp', 'Nnp', 'Adr']);
                    $details['BIC'] = $bic;
                    #Ensure some old or unused fields are removed
                    unset($details['NPSParticipant'], $details['ParticipantStatus']);
                    \ksort($details, \SORT_NATURAL);
                    #Prepare bindings
                    $bindings = [];
                    foreach (\array_keys($details) as $key) {
                        $bindings[':'.$key] = [
                            $details[$key],
                            ($details[$key] === NULL ? 'null' : 'string'),
                        ];
                    }
                    #Get current details
                    $current_details = $this->getBIC($bic);
                    #Check for Parent BIC
                    if (!empty($details['PrntBIC']) && count($this->getBIC($details['PrntBIC'])) === 0) {
                        $delay = true;
                    }
                    #Check if BIC exists at all
                    if (count($current_details) === 0) {
                        #We need to INSERT
                        $this->queries[] = [
                            'INSERT INTO `bic__list` (`BIC`, `DateIn`, `DateOut`, `Updated`, `NameP`, `EnglName`, `XchType`, `PtType`, `Srvcs`, `UID`, `PrntBIC`, `CntrCd`, `RegN`, `Ind`, `Rgn`, `Tnp`, `Nnp`, `Adr`) VALUES (:BIC, :DateIn, :DateOut, :file_date, :NameP, :EnglName, :XchType, :PtType, :Srvcs, :UID, :PrntBIC, :CntrCd, :RegN, :Ind, :Rgn, :Tnp, :Nnp, :Adr);',
                            \array_merge($bindings, [':file_date' => $this->file_date]),
                        ];
                    } elseif ($details !== $current_details) {
                        #Compare details if they are different - we need to update
                        $this->queries[] = [
                            'UPDATE `bic__list` SET `DateIn`=:DateIn, `DateOut`=:DateOut, `Updated`=:file_date, `NameP`=:NameP, `EnglName`=:EnglName, `XchType`=:XchType, `PtType`=:PtType, `Srvcs`=:Srvcs, `UID`=:UID, `PrntBIC`=:PrntBIC, `CntrCd`=:CntrCd, `RegN`=:RegN, `Ind`=:Ind, `Rgn`=:Rgn, `Tnp`=:Tnp, `Nnp`=:Nnp, `Adr`=:Adr WHERE `BIC`=:BIC;',
                            \array_merge($bindings, [':file_date' => $this->file_date]),
                        ];
                    }
                    #Process restrictions
                    if (count($restrictions) > 0) {
                        #Convert to array
                        $library_rest = [];
                        foreach ($restrictions as $restriction) {
                            $library_rest[] = Converters::attributesToArray($restriction);
                            \ksort($library_rest[\array_key_last($library_rest)]);
                        }
                        #Get current restrictions
                        $current_rest = $this->getRestrictions($bic);
                        #Check if any of the restrictions were removed
                        foreach ($current_rest as $restriction) {
                            if (!in_array($restriction, $library_rest, true)) {
                                #Update DateOut for restriction
                                $this->queries[] = $this->endRestriction($bic, $restriction);
                            }
                        }
                        #Add new restrictions
                        foreach ($library_rest as $restriction) {
                            if (!in_array($restriction, $current_rest, true)) {
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
                        $library_swift = [];
                        foreach ($swifts as $swift) {
                            $library_swift[] = Converters::attributesToArray($swift);
                            \ksort($library_swift[\array_key_last($library_swift)]);
                        }
                        #Get current SWIFTs
                        $current_swift = $this->getSWIFTs($bic);
                        #Add all SWIFTs. Updating the default flag if already existing
                        foreach ($library_swift as $swift) {
                            if (!in_array($swift, $current_swift, true)) {
                                #Insert restriction
                                $this->queries[] = [
                                    'INSERT INTO `bic__swift` (`BIC`, `SWBIC`, `DefaultSWBIC`, `DateIn`) VALUES (:BIC, :SWBIC, :DefaultSWBIC, :file_date) ON DUPLICATE KEY UPDATE `DefaultSWBIC`=:DefaultSWBIC;',
                                    [
                                        ':BIC' => $bic,
                                        ':SWBIC' => $swift['SWBIC'],
                                        ':DefaultSWBIC' => $swift['DefaultSWBIC'],
                                        ':file_date' => $this->file_date,
                                    ]
                                ];
                            }
                        }
                        #Close SWIFTs that do not match what we already have. If the default flag has been updated on a previous step, there will be no update here, because it will no longer match the condition
                        foreach ($current_swift as $swift) {
                            if (!in_array($swift, $library_swift, true)) {
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
                        $library_accounts = [];
                        $library_accounts_rest = [];
                        foreach ($accounts as $account) {
                            #Convert account
                            $library_accounts[] = Converters::attributesToArray($account, true, ['CK']);
                            #Set the last key
                            $last_key = \array_key_last($library_accounts);
                            unset($library_accounts[$last_key]['AccountStatus']);
                            \ksort($library_accounts[$last_key]);
                            #Convert restrictions
                            if (count($account->getElementsByTagName('AccRstrList')) > 0) {
                                foreach ($account->getElementsByTagName('AccRstrList') as $restriction) {
                                    $library_accounts_rest[$library_accounts[$last_key]['Account']][] = Converters::attributesToArray($restriction, true, ['SuccessorBIC']);
                                    \ksort($library_accounts_rest[$library_accounts[$last_key]['Account']]);
                                }
                            }
                        }
                        #"Remove" accounts
                        foreach ($this->getAccounts($bic) as $account) {
                            if (!in_array($account, $library_accounts, true)) {
                                $this->closeAccount($bic, $account['Account']);
                            }
                        }
                        #Update accounts
                        foreach ($library_accounts as $account) {
                            if (!empty($account['AccountCBRBIC']) && count($this->getBIC($account['AccountCBRBIC'])) === 0) {
                                $delay = true;
                            }
                            #Update account
                            $this->queries[] = [
                                'INSERT INTO `bic__accounts` (`BIC`, `Account`, `AccountCBRBIC`, `RegulationAccountType`, `CK`, `DateIn`) VALUES (:BIC, :Account, :AccountCBRBIC, :RegulationAccountType, :CK, :DateIn) ON DUPLICATE KEY UPDATE `AccountCBRBIC`=:AccountCBRBIC, `RegulationAccountType`=:RegulationAccountType, `CK`=:CK, `DateIn`=:DateIn, `DateOut`=NULL;',
                                [
                                    ':BIC' => $bic,
                                    ':Account' => $account['Account'],
                                    #There are known cases when BIC was set to '000000000' for some reason, thus we need to replace it with NULL. We also cover the possibility that it will not be present at all.
                                    ':AccountCBRBIC' => [
                                        ((int)$account['AccountCBRBIC'] === 0 ? NULL : $account['AccountCBRBIC']),
                                        ((int)$account['AccountCBRBIC'] === 0 ? 'null' : 'string'),
                                    ],
                                    ':RegulationAccountType' => $account['RegulationAccountType'],
                                    ':CK' => $account['CK'],
                                    ':DateIn' => $account['DateIn'],
                                ]
                            ];
                            if (!empty($library_accounts_rest[$account['Account']])) {
                                #Get current restrictions
                                $current_rest = $this->getAccountRestrictions($account['Account']);
                                #Add all new restrictions
                                foreach ($library_accounts_rest[$account['Account']] as $restriction) {
                                    if (!in_array($restriction, $current_rest, true)) {
                                        if (!empty($restriction['SuccessorBIC']) && count($this->getBIC($restriction['SuccessorBIC'])) === 0) {
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
                                #Check if any of the restrictions were removed
                                foreach ($current_rest as $restriction) {
                                    if (!in_array($restriction, $library_accounts_rest[$account['Account']], true)) {
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
                    #If the flag is true, it means that there is a dependency on a BIC, which is not yet present; thus we need to run the queries after BIC is added, but since we can't predict when it will be added, we do this outside of the loop.
                    if ($delay) {
                        $delayed[] = $this->queries;
                    } elseif (!Query::query($this->queries)) {
                        #Apply queries for this BIC
                        throw new \RuntimeException('Failed to update `'.$bic.'` from `'.$download.'`');
                    }
                }
                #Replace list of queries with delayed queries
                $this->queries = \array_merge(...$delayed);
                #Check for removed BICs
                foreach ($this->getBICs() as $bic) {
                    if (!in_array($bic, $bics, true)) {
                        #Close bic
                        $this->closeBIC($bic);
                    }
                }
                #Reset the default flag for SWIFTs with DateOut (the way data is presented by CB, it's possible for them to have an incorrect flag).
                $this->queries[] = ['UPDATE `bic__swift` SET `DefaultSWBIC`=0 WHERE `DateOut` IS NOT NULL;'];
                #Set `DateIn` for "bad" entries. We are assuming that affected entries were added at least at the time of BIC library creation. Another case of "bad" data.
                $this->queries[] = ['UPDATE `bic__list` SET `DateIn`=\'1996-07-10\' WHERE `DateIn` IS NULL OR `DateIn`=\'1970-01-01\';'];
                $this->queries[] = ['UPDATE `bic__accounts` SET `DateIn`=\'1996-07-10\' WHERE `DateIn`=\'1970-01-01\';'];
                $this->queries[] = [
                    'UPDATE `bic__settings` SET `value`=:date WHERE `setting`=\'date\';',
                    [':date' => $lib_date->format('d.m.Y')],
                ];
                #Run queries for BICs removals and library update
                Query::query($this->queries);
                $this->log($lib_date->format('d.m.Y'), 'Успешное обновление', $manual);
                if ($manual && $lib_date->format('Y-m-d') !== $lib_date_initial) {
                    return $lib_date->getTimestamp();
                }
                #Increase by 1 day
                $lib_date->add(new \DateInterval('P1D'));
            } catch (\Throwable $exception) {
                $error = $exception->getMessage()."\r\n".$exception->getTraceAsString();
                $this->log($lib_date->format('d.m.Y'), $error, $manual);
                return $error;
            } finally {
                #Remove all library-related files if any were identified
                \array_map('\unlink', \glob(\sys_get_temp_dir().'/*_ED807_full.*', \GLOB_NOSORT));
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
        $result = Query::query(
            'SELECT `BIC`, `DateIn`, `DateOut`, `NameP`, `EnglName`, `XchType`, `PtType`, `Srvcs`, `UID`, `PrntBIC`, `CntrCd`, `RegN`, `Ind`, `Rgn`, `Tnp`, `Nnp`, `Adr` FROM `bic__list` WHERE `BIC`=:BIC;',
            [':BIC' => $bic,], return: 'row'
        );
        if ($result !== []) {
            \ksort($result, \SORT_NATURAL);
            #Pad BICs with zeros
            $result['BIC'] = mb_str_pad((string)$result['BIC'], 9, '0', \STR_PAD_LEFT, 'UTF-8');
            if ($result['PrntBIC'] !== NULL) {
                $result['PrntBIC'] = mb_str_pad((string)$result['PrntBIC'], 9, '0', \STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get restrictions
     */
    private function getRestrictions(string $bic): array
    {
        return Query::query(
            'SELECT `Rstr`, `RstrDate` FROM `bic__bic_rstr` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,], return: 'all'
        );
    }
    
    /**
     * Get SWIFT accounts
     */
    private function getSWIFTs(string $bic): array
    {
        return Query::query(
            'SELECT `DefaultSWBIC`, `SWBIC` FROM `bic__swift` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,], return: 'all'
        );
    }
    
    /**
     * #Get accounts
     */
    private function getAccounts(string $bic): array
    {
        $result = Query::query(
            'SELECT `Account`, `AccountCBRBIC`, `CK`, `DateIn`, `RegulationAccountType` FROM `bic__accounts` WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [':BIC' => $bic,], return: 'all'
        );
        #Pad BICs with zeros
        foreach ($result as $key => $account) {
            if ($account['AccountCBRBIC'] !== NULL) {
                $result[$key]['AccountCBRBIC'] = mb_str_pad((string)$account['AccountCBRBIC'], 9, '0', \STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get account restrictions
     */
    private function getAccountRestrictions(string $account): array
    {
        $result = Query::query(
            'SELECT `AccRstr`, `AccRstrDate`, `SuccessorBIC` FROM `bic__acc_rstr` WHERE `Account`=:Account;',
            [':Account' => $account,], return: 'all'
        );
        foreach ($result as $key => $restriction) {
            if ($restriction['SuccessorBIC'] !== NULL) {
                $result[$key]['SuccessorBIC'] = mb_str_pad((string)$restriction['SuccessorBIC'], 9, '0', \STR_PAD_LEFT, 'UTF-8');
            }
        }
        return $result;
    }
    
    /**
     * Get all BICs
     */
    private function getBICs(): array
    {
        return Query::query('SELECT `BIC` FROM `bic__list` WHERE `DateOut` IS NULL;', return: 'column');
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
            'UPDATE `bic__list` SET `DateOut`=:file_date WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
            [
                ':BIC' => $bic,
                ':file_date' => $this->file_date,
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
    private function endRestriction(string $bic, ?array $restriction = null): array
    {
        if (Sanitize::whiteString($bic)) {
            return [];
        }
        #If no details, assume we are ending all restrictions
        if ($restriction === null || $restriction === []) {
            return [
                'UPDATE `bic__bic_rstr` SET `DateOut`=:file_date WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':file_date' => $this->file_date,
                ]
            ];
        }
        #Otherwise, use details to narrow down
        if (!isset($restriction['Rstr'], $restriction['RstrDate'])) {
            return [];
        }
        return [
            'UPDATE `bic__bic_rstr` SET `DateOut`=:file_date WHERE `BIC`=:BIC AND `Rstr`=:Rstr AND `RstrDate`=:RstrDate;',
            [
                ':BIC' => $bic,
                ':Rstr' => $restriction['Rstr'],
                ':RstrDate' => $restriction['RstrDate'],
                ':file_date' => $this->file_date,
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
        if (Sanitize::whiteString($bic)) {
            return [];
        }
        #If swift is empty, assume that we are removing all accounts
        if ($swift === null || Sanitize::whiteString($swift)) {
            return [
                'UPDATE `bic__swift` SET `DateOut`=:file_date, `DefaultSWBIC`=0 WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':file_date' => $this->file_date,
                ]
            ];
        }
        return [
            'UPDATE `bic__swift` SET `DateOut`=:file_date, `DefaultSWBIC`=0 WHERE `BIC`=:BIC AND `SWBIC`=:SWBIC AND `DefaultSWBIC`=:DefaultSWBIC;',
            [
                ':BIC' => $bic,
                ':SWBIC' => $swift,
                ':DefaultSWBIC' => [$default, 'bool'],
                ':file_date' => $this->file_date,
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
        #If an account is empty, assume that we are removing all accounts
        if ($account === null || Sanitize::whiteString($account)) {
            #End restrictions
            $this->queries[] = $this->endAccountRestriction($bic);
            #Close all open accounts
            $this->queries[] = [
                'UPDATE `bic__accounts` SET `DateOut`=:file_date WHERE `BIC`=:BIC AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':file_date' => $this->file_date,
                ]
            ];
        } else {
            #End restrictions
            $this->queries[] = $this->endAccountRestriction($account, true);
            #Close account
            $this->queries[] = [
                'UPDATE `bic__accounts` SET `DateOut`=:file_date WHERE `BIC`=:BIC AND `Account`=:Account AND `DateOut` IS NULL;',
                [
                    ':BIC' => $bic,
                    ':Account' => $account,
                    ':file_date' => $this->file_date,
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
        if (Sanitize::whiteString($bic)) {
            return [];
        }
        #If the account flag is true, we know the account
        if ($account) {
            #If no details, end all restrictions
            if ($restriction === null || $restriction === []) {
                return [
                    'UPDATE `bic__acc_rstr` SET `DateOut`=:file_date WHERE `Account`=:Account AND `DateOut` IS NULL;',
                    [
                        ':Account' => $bic,
                        ':file_date' => $this->file_date,
                    ]
                ];
            }
            #Otherwise, use details to narrow down
            if (!isset($restriction['AccRstr'], $restriction['AccRstrDate'])) {
                return [];
            }
            return [
                'UPDATE `bic__acc_rstr` SET `DateOut`=:file_date WHERE `Account`=:Account AND `AccRstr`=:AccRstr AND `AccRstrDate`=:AccRstrDate;',
                [
                    ':Account' => $bic,
                    ':AccRstr' => $restriction['AccRstr'],
                    ':AccRstrDate' => $restriction['AccRstrDate'],
                    ':file_date' => $this->file_date,
                ]
            ];
        }
        #Otherwise, we are removing everything for the whole BIC
        return [
            'UPDATE `bic__acc_rstr` SET `DateOut`=:file_date WHERE `DateOut` IS NULL AND `Account` IN (SELECT `Account` FROM `bic__accounts` WHERE `BIC`=:BIC AND `DateOut` IS NULL);',
            [
                ':BIC' => $bic,
                ':file_date' => $this->file_date,
            ]
        ];
    }
    
    /**
     * Function to log updates
     *
     * @param string $bic_date
     * @param string $message
     * @param bool   $manual
     *
     * @return void
     */
    private function log(string $bic_date, string $message, bool $manual = false): void
    {
        Security::log(LogType::BICTracker->value, ($manual ? 'Manual' : 'Cron').' update', $message.' ('.$bic_date.')', (!$manual ? SystemUser::System->value : $_SESSION['user_id'] ?? null));
    }
    
    /**
     * Function to download BIC
     */
    private function download(\DateTime $date): bool|string
    {
        #Generate the zip path
        $file_name = \sys_get_temp_dir().'/'.$date->format('Ymd').'_ED807_full.xml';
        #Generate link
        $link = self::BIC_DOWN_BASE.$date->format('d.m.Y');
        $data = new Curl('BIC Tracker (https://github.com/Simbiat/BIC-Tracker)')->getPage($link);
        if (!\is_string($data)) {
            return false;
        }
        #Load page as DOM Document
        \libxml_use_internal_errors(true);
        $page = new \DOMDocument();
        $page->loadHTML($data);
        #Iterate links to find the one we need
        foreach ($page->getElementsByTagName('a') as $anchor) {
            #Filter only those that has proper value
            if (\preg_match('/\s*Справочник БИК\s*/iu', $anchor->textContent) === 1) {
                #Get href attribute
                $href = $anchor->getAttribute('href');
                #Skip the link for "current" library
                if (\preg_match('/\/s\/newbik/iu', $href) === 0) {
                    $href = self::BIC_BASE_HREF.$href;
                    #Attempt to actually download the zip file
                    $bic_file = new Curl('BIC Tracker (https://github.com/Simbiat/BIC-Tracker)')->getFile($href);
                    if (\is_array($bic_file) && !empty($bic_file['server_name'])) {
                        $bic_file = $bic_file['server_path'].'/'.$bic_file['server_name'];
                    } else {
                        return false;
                    }
                    #Unzip the file
                    if (\is_file($bic_file)) {
                        $zip = new \ZipArchive();
                        if ($zip->open($bic_file) === true) {
                            $zip->extractTo(\sys_get_temp_dir());
                            $zip->close();
                        }
                        #Remove zip file
                        /** @noinspection PhpUsageOfSilenceOperatorInspection */
                        @\unlink($bic_file);
                        #Check if the ED807 file exists
                        if (\file_exists($file_name)) {
                            return $file_name;
                        }
                        return false;
                    }
                    return true;
                }
            }
        }
        #This means that no file was found for the date (which is not necessarily a problem)
        return true;
    }
    
    /**
     * Function to get the current library date
     * @return \DateTime
     */
    public function bicDate(): \DateTime
    {
        try {
            $date = Query::query('SELECT `value` FROM `bic__settings` WHERE `setting`=\'date\';', return: 'value');
            return \DateTime::createFromFormat('d.m.Y', $date);
        } catch (\Throwable) {
            return \DateTime::createFromTimestamp(\time());
        }
    }
}

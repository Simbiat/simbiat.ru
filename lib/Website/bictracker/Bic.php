<?php
declare(strict_types = 1);

namespace Simbiat\Website\bictracker;

use Simbiat\Website\Abstracts\Entity;
use Simbiat\ArrayHelpers;
use Simbiat\Website\Config;

/**
 * Class representing a Bank Identification Code (BIC)
 */
class Bic extends Entity
{
    #Custom properties
    #Bank code of the entity
    public string $BIC;
    #Name of the entity
    public string $NameP;
    #Bank code of parent entity
    public null|string|array $PrntBIC = null;
    #English name of the entity
    public ?string $EnglName = null;
    #Whether entity is active or not
    public bool|int|string $XchType = false;
    #Old BIC, in case it was used by several organizations
    public ?string $OLD_NEWNUM = null;
    #UID for electronic messages used by organization
    public ?string $UID = null;
    #Country code
    public ?string $CntrCd = null;
    #Address
    public ?string $Adr = null;
    #Date added
    public ?string $DateIn = null;
    #Date removed from official library
    public ?string $DateOut = null;
    #Date of the latest update
    public ?string $Updated = null;
    #Postal index
    public ?string $Ind = null;
    #Types of services provided
    public ?string $Srvcs = null;
    #Name of the location
    public ?string $Nnp = null;
    #Type of location
    public ?string $Tnp = null;
    #Type of entity
    public ?string $PtType = null;
    #Registration number
    public ?string $RegN = null;
    #Location region
    public ?string $Rgn = null;
    #Processing center
    public ?string $CENTER = null;
    #List of branches if any
    public array $branches = [];
    #List of SWIFT codes if any
    public array $SWIFTs = [];
    #Restrictions on whole organization
    public array $restrictions = [];
    #List of accounts
    public array $accounts = [];
    #List of organizations with same BIC used in the past
    public array $sameBIC = [];
    #List of organizations using same address
    public array $sameAddress = [];
    #Number of entities serviced by entity
    public int $serviceFor = 0;
    #Old data from DBF files
    public array $DBF = [];
    
    /**
     * Get BIC data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        return Config::$dbController->selectRow('SELECT `biclist`.`VKEY`, `VKEYDEL`, `BVKEY`, `FVKEY`, `OLD_NEWNUM`, `EnglName`, `XchType`, `UID`, `CntrCd`, `Adr`, `AT1`, `AT2`, `CKS`, `DATE_CH`, `DateIn`, `DateOut`, `Updated`, `Ind`, `bic__srvcs`.`Description` AS `Srvcs`, `NameP`, `NAMEMAXB`, `NEWKS`, biclist.`BIC`, `PrntBIC`, `SWIFT_NAME`, `Nnp`, `OKPO`, `PERMFO`, `bic__pzn`.`NAME` AS `PtType`, `bic__rclose`.`NAMECLOSE` AS `R_CLOSE`, `RegN`, `bic__reg`.`NAME` AS `Rgn`, `bic__reg`.`CENTER`, `RKC`, `SROK`, `TELEF`, `Tnp`, `PRIM1`, `PRIM2`, `PRIM3` FROM `bic__list` biclist
            LEFT JOIN `bic__reg` ON `bic__reg`.`RGN` = `biclist`.`Rgn`
            LEFT JOIN `bic__pzn` ON `bic__pzn`.`PtType` = `biclist`.`PtType`
            LEFT JOIN `bic__rclose` ON `bic__rclose`.`R_CLOSE` = `biclist`.`R_CLOSE`
            LEFT JOIN `bic__srvcs` ON `bic__srvcs`.`Srvcs` = `biclist`.`Srvcs`
            WHERE biclist.`BIC` = :BIC', [':BIC' => $this->id]);
    }
    
    /**
     * Function to return current data about the bank
     * @throws \Exception
     */
    protected function process(array $fromDB): void
    {
        #Pad stuff
        $fromDB['BIC'] = $this->padBic((string)$fromDB['BIC']);
        if (!empty($fromDB['PrntBIC'])) {
            $fromDB['PrntBIC'] = $this->padBic((string)$fromDB['PrntBIC']);
        }
        if (!empty($fromDB['RKC'])) {
            $fromDB['RKC'] = $this->padBic((string)$fromDB['RKC']);
        }
        if (!empty($fromDB['OLD_NEWNUM'])) {
            $fromDB['OLD_NEWNUM'] = $this->padBic((string)$fromDB['OLD_NEWNUM']);
        }
        #Get authorized branch
        if (!empty($fromDB['PrntBIC'])) {
            $fromDB['PrntBIC'] = $this->bicUf($fromDB['PrntBIC']);
        }
        #Get all branches of the bank (if any)
        $fromDB['branches'] = $this->getBranches($fromDB['BIC']);
        $fromDB['branches'] = ArrayHelpers::MultiArrSort($fromDB['branches'], 'name');
        #Get SWIFT codes
        $fromDB['SWIFTs'] = Config::$dbController->selectAll('SELECT `SWBIC`, `DefaultSWBIC`, `DateIn`, `DateOut` FROM `bic__swift` WHERE `BIC`=:BIC ORDER BY `DefaultSWBIC` DESC, `DateOut` DESC', [':BIC' => $this->id]);
        #Get restrictions for BIC
        $fromDB['restrictions'] = Config::$dbController->selectAll('SELECT `bic__bic_rstr`.`Rstr` as `name`, `Description` as `description`, `RstrDate` as `startTime`, `DateOut` as `endTime` FROM `bic__bic_rstr` LEFT JOIN `bic__rstr` ON `bic__bic_rstr`.`Rstr`=`bic__rstr`.`Rstr` WHERE `BIC`=:BIC ORDER BY `RstrDate` DESC;', [':BIC' => $this->id]);
        #Get accounts
        $fromDB['accounts'] = Config::$dbController->selectAll(
            'SELECT `Account`, `bic__acc_type`.`Description` as `AccountType`, `CK`, `DateIn`, `DateOut`, `AccountCBRBIC` FROM `bic__accounts`
                LEFT JOIN `bic__acc_type` ON `bic__accounts`.`RegulationAccountType`=`bic__acc_type`.`RegulationAccountType` WHERE `bic__accounts`.`BIC`=:BIC',
            [':BIC' => $this->id]
        );
        foreach ($fromDB['accounts'] as $key => $account) {
            #Get restrictions
            $fromDB['accounts'][$key]['restrictions'] = Config::$dbController->selectAll('SELECT `Description` as `Restriction`, `AccRstrDate` as `DateIn`, `DateOut`, `SuccessorBIC` FROM `bic__acc_rstr` LEFT JOIN `bic__rstr` ON `bic__acc_rstr`.`AccRstr`=`bic__rstr`.`Rstr` WHERE `account`=:account ORDER BY `AccRstrDate` DESC;', [':account' => $account['Account']]);
            #Get successor details for restrictions
            foreach ($fromDB['accounts'][$key]['restrictions'] as $keyRstr => $restriction) {
                if (!empty($restriction['SuccessorBIC'])) {
                    $fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC'] = Config::$dbController->selectRow('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `BIC`=:BIC;', [':BIC' => $this->padBic((string)$restriction['SuccessorBIC'])]);
                    $fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC'] = $this->padBic((string)$fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC']);
                }
            }
        }
        #Count banks, that are serviced by this one
        $fromDB['serviceFor'] = Config::$dbController->count('SELECT COUNT(*) AS `count` FROM `bic__accounts` WHERE `AccountCBRBIC`=:BIC', [':BIC' => $this->id]);
        #Get list of banks, that used same BIC
        $fromDB['sameBIC'] = Config::$dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `OLD_NEWNUM`=:NEWNUM AND `BIC`<>:BIC;', [':NEWNUM' => $fromDB['OLD_NEWNUM'] ?? $fromDB['BIC'], ':BIC' => $fromDB['BIC']]);
        #Get list of banks on same address
        $fromDB['sameAddress'] = Config::$dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `Adr`=:Adr AND `BIC`<>:BIC;', [':Adr' => $fromDB['Adr'], ':BIC' => $this->id]);
        
        #Old DBF data processing
        #Get list of phones
        if (!empty($fromDB['TELEF'])) {
            $fromDB['TELEF'] = $this->phoneList($fromDB['TELEF']);
        } else {
            $fromDB['TELEF'] = [];
        }
        #If RKC=BIC it means, that current bank is RKC and does not have bank above it
        if ($fromDB['RKC'] === $fromDB['BIC']) {
            $fromDB['RKC'] = NULL;
        }
        #Chains based on DBF data
        if (!empty($fromDB['RKC'])) {
            $fromDB['RKC'] = $this->rkcChain($fromDB['RKC']);
        }
        #Get the chain of predecessors (if any) based on DBF data
        $fromDB['DBF']['predecessors'] = (empty($fromDB['VKEY']) ? [] : $this->predecessors($fromDB['VKEY']));
        $fromDB['DBF']['predecessors'] = ArrayHelpers::MultiArrSort($fromDB['DBF']['predecessors'], 'name');
        #Get the chain of successors (if any) based on DBF data
        $fromDB['DBF']['successors'] = (empty($fromDB['VKEYDEL']) ? [] : $this->successors($fromDB['VKEYDEL']));
        #Moving DBF related values around
        foreach (['NAMEMAXB', 'NAMEN', 'SWIFT_NAME'] as $key) {
            ArrayHelpers::moveToSubarray($fromDB, $key, ['DBF', 'names', $key]);
        }
        foreach (['AT1', 'AT2', 'TELEF', 'CKS'] as $key) {
            ArrayHelpers::moveToSubarray($fromDB, $key, ['DBF', 'contacts', $key]);
        }
        foreach (['R_CLOSE', 'PRIM1', 'PRIM2', 'PRIM3'] as $key) {
            ArrayHelpers::moveToSubarray($fromDB, $key, ['DBF', 'removal', $key]);
        }
        foreach (['DATE_CH', 'VKEY', 'VKEYDEL', 'BVKEY', 'FVKEY', 'RKC', 'SROK', 'NEWKS', 'OKPO', 'PERMFO'] as $key) {
            ArrayHelpers::moveToSubarray($fromDB, $key, ['DBF', 'misc', $key]);
        }
        #If RKC equals headquarters - remove it. For newer entries, they were essentially replaced
        if ($fromDB['DBF']['misc']['RKC'] === $fromDB['PrntBIC']) {
            $fromDB['DBF']['misc']['RKC'] = NULL;
        }
        #Convert array to properties
        $this->arrayToProperties($fromDB);
    }
    
    /**
     * Function to get list of all predecessors (direct or not)
     * @throws \Exception
     */
    private function predecessors(string $vkey): array
    {
        $banks = Config::$dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `VKEY` FROM `bic__list` WHERE `VKEYDEL` = :BIC ORDER BY `NameP`', [':BIC' => $vkey]);
        if (empty($banks)) {
            $banks = [];
        } else {
            foreach ($banks as $key => $bank) {
                $banks[$key]['id'] = $this->padBic((string)$bank['id']);
                $predecessor = $this->predecessors($bank['VKEY']);
                if (!empty($predecessor)) {
                    $banks = array_merge($banks, $predecessor);
                }
            }
        }
        return $banks;
    }
    
    /**
     * Function to get all successors (each as a chain)
     * @throws \Exception
     */
    private function successors(string $vkey): array
    {
        #Get initial list
        $bank = Config::$dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `VKEYDEL`, `VKEY`, `DateOut` FROM `bic__list` WHERE `VKEY` = :BIC ORDER BY `NameP`', [':BIC' => $vkey]);
        if (empty($bank)) {
            $bank = [];
        } else {
            #Get successors for each successor
            foreach ($bank as $key => $item) {
                $bank[$key]['id'] = $this->padBic((string)$item['id']);
                if (!empty($item[0]['VKEYDEL']) && $item[0]['VKEYDEL'] !== $vkey && $bank[0]['VKEYDEL'] !== $bank[0]['VKEY']) {
                    $bank[$key] = array_merge($item, $this->successors($item[0]['id']));
                }
            }
        }
        return $bank;
    }
    
    /**
     * Function to get all RKCs for a bank as a chain
     * @throws \Exception
     */
    private function rkcChain(string $bic): array
    {
        $banks = [];
        #Get initial list
        $bank = Config::$dbController->selectRow('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `bic__list` WHERE `BIC` = :BIC', [':BIC' => $bic]);
        if (empty($bank)) {
            return $banks;
        }
        $bank['id'] = $this->padBic((string)$bank['id']);
        if (!empty($bank['RKC'])) {
            $bank['RKC'] = $this->padBic((string)$bank['RKC']);
        }
        if (!empty($bank['PrntBIC'])) {
            $bank['PrntBIC'] = $this->padBic((string)$bank['PrntBIC']);
        }
        $banks[] = $bank;
        #Get RKC for RKC
        if (!empty($bank['RKC']) && $bank['RKC'] !== $bic && $bank['RKC'] !== $bank['id']) {
            $banks = array_merge($banks, $this->rkcChain($bank['RKC']));
        }
        return $banks;
    }
    
    /**
     * Function to get authorized branches as a chain
     * @throws \Exception
     */
    private function bicUf(string $bic): array
    {
        $banks = [];
        #Get initial list
        $bank = Config::$dbController->selectRow('SELECT \'bic\' as `type`,`BIC` as `id`,`NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `bic__list` WHERE `BIC` = :BIC', [':BIC' => $bic]);
        if (empty($bank)) {
            return $banks;
        }
        $bank['id'] = $this->padBic((string)$bank['id']);
        if (!empty($bank['PrntBIC'])) {
            $bank['PrntBIC'] = $this->padBic((string)$bank['PrntBIC']);
        }
        if (!empty($bank['RKC'])) {
            $bank['RKC'] = $this->padBic((string)$bank['RKC']);
        }
        $banks[] = $bank;
        #Get authorized branch of authorized branch
        if (!empty($bank['PrntBIC']) && $bank['PrntBIC'] !== $bic && !empty($bank['id']) && $bank['PrntBIC'] !== $bank['id']) {
            $banks = array_merge($banks, $this->bicUf($bank['PrntBIC']));
        }
        return $banks;
    }
    
    /**
     * Function to get all branches of a bank
     * @throws \Exception
     */
    private function getBranches(string $bic): array
    {
        $banks = Config::$dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `BIC`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `PrntBIC` = :BIC ORDER BY `NameP`;', [':BIC' => $bic]);
        if (empty($banks)) {
            $banks = [];
        } else {
            foreach ($banks as $key => $bank) {
                $banks[$key]['id'] = $this->padBic((string)$bank['id']);
                $predecessor = $this->getBranches($bank['id']);
                if (!empty($predecessor)) {
                    $banks = array_merge($banks, $predecessor);
                }
            }
        }
        return $banks;
    }
    
    /**
     * Function to format list of phones
     * @param string $phoneString
     *
     * @return array
     */
    private function phoneList(string $phoneString): array
    {
        #Remove empty brackets
        #Remvoe pager notation (obsolete)
        #Update Moscow code
        $phoneString = str_replace(['()', 'ПЕЙД', '(095)'], ['', '', '(495)'], $phoneString);
        #Attempt to get additional number (to be entered after you've dialed-in)
        $dob = explode(',ДОБ.', $phoneString);
        if (empty($dob[1])) {
            $dob = explode(',ДБ.', $phoneString);
            if (empty($dob[1])) {
                $dob = explode('(ДОБ.', $phoneString);
                if (empty($dob[1])) {
                    $dob = explode(' ДОБ.', $phoneString);
                    if (empty($dob[1])) {
                        $dob = explode('ДОБ', $phoneString);
                        if (empty($dob[1])) {
                            $dob = explode(' код ', $phoneString);
                            if (empty($dob[1])) {
                                $dob = explode(',АБ.', $phoneString);
                                if (empty($dob[1])) {
                                    $dob = explode(',Д.', $phoneString);
                                    if (empty($dob[1])) {
                                        $dob = explode('(Д.', $phoneString);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        #Check if there are additional numbers
        if (empty($dob[1])) {
            $dobs = '';
        } else {
            #Remove all letters from additional number
            $dobs = preg_replace('/[^\d,]/', '', $dob[1]);
            #Replace ','. To be honest not sure why it's done through explode/implode, but I think this helped with removing empty values
            $dobs = explode(',', $dobs);
            $dobs = implode(' или ', $dobs);
        }
        #Get actual phones
        $phones = explode(',', $dob[0]);
        #Attempting to sanitize the phone numbers to utilize +7 code only
        preg_match('/\((\d*)\)/', $phones[0], $code);
        if (empty($code[1])) {
            $code = '+7 ';
        } else {
            $code = '+7 ('.$code[1].') ';
        }
        foreach ($phones as $key => $phone) {
            if (preg_match('/\((\d*)\)/', $phone)) {
                $phone = '+7 '.$phone;
                if (!preg_match('/\) /', $phone)) {
                    $phone = preg_replace('/\)/', ') ', $phone);
                }
            } else {
                $phone = $code.$phone;
            }
            $phones[$key] = ['phone' => $phone, 'url' => preg_replace('/[^\d+]/', '', $phone)];
        }
        return ['phones' => $phones, 'dob' => $dobs];
    }
    
    /**
     * Pad BICs with zeros
     * @param string $bic
     *
     * @return string
     */
    private function padBic(string $bic): string
    {
        return mb_str_pad($bic, 9, '0', STR_PAD_LEFT, 'UTF-8');
    }
}

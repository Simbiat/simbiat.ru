<?php

declare(strict_types=1);
namespace Simbiat\bictracker;

use Simbiat\Abstracts\Entity;
use Simbiat\ArrayHelpers;

class Bic extends Entity
{
    protected const dbPrefix = 'bic__';

    #Custom properties
    #Bank code of the entity
    public string $BIC;
    #Name of the entity
    public string $NameP;
    #Bank code of parent entity
    public ?string $PrntBIC = null;
    #English name of the entity
    public ?string $EnglName = null;
    #Whether entity is active or not
    public bool|int $XchType = false;
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

    protected function getFromDB(): array
    {
        return $this->dbController->selectRow('SELECT `biclist`.`VKEY`, `VKEYDEL`, `BVKEY`, `FVKEY`, `OLD_NEWNUM`, `EnglName`, `XchType`, `UID`, `CntrCd`, `Adr`, `AT1`, `AT2`, `CKS`, `DATE_CH`, `DateIn`, `DateOut`, `Updated`, `Ind`, `' . self::dbPrefix . 'srvcs`.`Description` AS `Srvcs`, `NameP`, `NAMEMAXB`, `NEWKS`, biclist.`BIC`, `PrntBIC`, `SWIFT_NAME`, `Nnp`, `OKPO`, `PERMFO`, `' . self::dbPrefix . 'pzn`.`NAME` AS `PtType`, `' . self::dbPrefix . 'rclose`.`NAMECLOSE` AS `R_CLOSE`, `RegN`, `' . self::dbPrefix . 'reg`.`NAME` AS `Rgn`, `' . self::dbPrefix . 'reg`.`CENTER`, `RKC`, `SROK`, `TELEF`, `Tnp`, `PRIM1`, `PRIM2`, `PRIM3` FROM `' . self::dbPrefix . 'list` biclist
            LEFT JOIN `' . self::dbPrefix . 'reg` ON `' . self::dbPrefix . 'reg`.`RGN` = biclist.`Rgn`
            LEFT JOIN `' . self::dbPrefix . 'pzn` ON `' . self::dbPrefix . 'pzn`.`PtType` = biclist.`PtType`
            LEFT JOIN `' . self::dbPrefix . 'rclose` ON `' . self::dbPrefix . 'rclose`.`R_CLOSE` = biclist.`R_CLOSE`
            LEFT JOIN `' . self::dbPrefix . 'srvcs` ON `' . self::dbPrefix . 'srvcs`.`Srvcs` = biclist.`Srvcs`
            WHERE biclist.`BIC` = :BIC', [':BIC' => $this->id]);
    }
    #Function to return current data about the bank
    /**
     * @throws \Exception
     */
    protected function process(array $fromDB): void
    {
        $arrayHelpers = (new ArrayHelpers());
        #Pad stuff
        $fromDB['BIC'] = $this->padBic($fromDB['BIC']);
        if (!empty($fromDB['PrntBIC'])) {
            $fromDB['PrntBIC'] = $this->padBic($fromDB['PrntBIC']);
        }
        if (!empty($fromDB['RKC'])) {
            $fromDB['RKC'] = $this->padBic($fromDB['RKC']);
        }
        if (!empty($fromDB['OLD_NEWNUM'])) {
            $fromDB['OLD_NEWNUM'] = $this->padBic($fromDB['OLD_NEWNUM']);
        }
        #Get authorized branch
        if (!empty($fromDB['PrntBIC'])) {
            $fromDB['PrntBIC'] = $this->bicUf($fromDB['PrntBIC']);
        }
        #Get all branches of the bank (if any)
        $fromDB['branches'] = $this->getBranches($fromDB['BIC']);
        $fromDB['branches'] = $arrayHelpers->MultiArrSort($fromDB['branches'], 'name');
        #Get SWIFT codes
        $fromDB['SWIFTs'] = $this->dbController->selectAll('SELECT `SWBIC`, `DefaultSWBIC`, `DateIn`, `DateOut` FROM `' . self::dbPrefix . 'swift` WHERE `BIC`=:BIC ORDER BY `DefaultSWBIC` DESC, `DateOut` DESC', [':BIC' => $this->id]);
        #Get restrictions for BIC
        $fromDB['restrictions'] = $this->dbController->selectAll('SELECT `' . self::dbPrefix . 'bic_rstr`.`Rstr` as `name`, `Description` as `description`, `RstrDate` as `startTime`, `DateOut` as `endTime` FROM `' . self::dbPrefix . 'bic_rstr` LEFT JOIN `' . self::dbPrefix . 'rstr` ON `' . self::dbPrefix . 'bic_rstr`.`Rstr`=`' . self::dbPrefix . 'rstr`.`Rstr` WHERE `BIC`=:BIC ORDER BY `RstrDate` DESC;', [':BIC' => $this->id]);
        #Get accounts
        $fromDB['accounts'] = $this->dbController->selectAll(
            'SELECT `Account`, `' . self::dbPrefix . 'acc_type`.`Description` as `AccountType`, `CK`, `DateIn`, `DateOut`, `AccountCBRBIC` FROM `' . self::dbPrefix . 'accounts`
                LEFT JOIN `' . self::dbPrefix . 'acc_type` ON `' . self::dbPrefix . 'accounts`.`RegulationAccountType`=`' . self::dbPrefix . 'acc_type`.`RegulationAccountType` WHERE `' . self::dbPrefix . 'accounts`.`BIC`=:BIC',
            [':BIC' => $this->id]
        );
        foreach ($fromDB['accounts'] as $key => $account) {
            #Get restrictions
            $fromDB['accounts'][$key]['restrictions'] = $this->dbController->selectAll('SELECT `Description` as `Restriction`, `AccRstrDate` as `DateIn`, `DateOut`, `SuccessorBIC` FROM `' . self::dbPrefix . 'acc_rstr` LEFT JOIN `' . self::dbPrefix . 'rstr` ON `' . self::dbPrefix . 'acc_rstr`.`AccRstr`=`' . self::dbPrefix . 'rstr`.`Rstr` WHERE `account`=:account ORDER BY `AccRstrDate` DESC;', [':account' => $account['Account']]);
            #Get successor details for restrictions
            foreach ($fromDB['accounts'][$key]['restrictions'] as $keyRstr => $restriction) {
                if (!empty($restriction['SuccessorBIC'])) {
                    $fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC'] = $this->dbController->selectRow('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `' . self::dbPrefix . 'list` WHERE `BIC`=:BIC;', [':BIC' => $this->padBic($restriction['SuccessorBIC'])]);
                    $fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC'] = $this->padBic($fromDB['accounts'][$key]['restrictions'][$keyRstr]['SuccessorBIC']);
                }
            }
        }
        #Count banks, that are serviced by this one
        $fromDB['serviceFor'] = $this->dbController->Count('SELECT COUNT(*) FROM `' . self::dbPrefix . 'accounts` WHERE `AccountCBRBIC`=:BIC', [':BIC' => $this->id]);
        #Get list of banks, that used same BIC
        $fromDB['sameBIC'] = $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `' . self::dbPrefix . 'list` WHERE `OLD_NEWNUM`=:NEWNUM AND `BIC`<>:BIC;', [':NEWNUM' => $fromDB['OLD_NEWNUM'] ?? $this->id, ':BIC' => $this->id]);
        #Get list of banks on same address
        $fromDB['sameAddress'] = $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `' . self::dbPrefix . 'list` WHERE `Adr`=:Adr AND `BIC`<>:BIC;', [':Adr' => $fromDB['Adr'], ':BIC' => $this->id]);


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
        $fromDB['DBF']['predecessors'] = $arrayHelpers->MultiArrSort($fromDB['DBF']['predecessors'], 'name');
        #Get the chain of successors (if any) based on DBF data
        $fromDB['DBF']['successors'] = (empty($fromDB['VKEYDEL']) ? [] : $this->successors($fromDB['VKEYDEL']));
        #Moving DBF related values around
        foreach (['NAMEMAXB', 'NAMEN', 'SWIFT_NAME'] as $key) {
            $arrayHelpers->moveToSubarray($fromDB, $key, ['DBF', 'names', $key]);
        }
        foreach (['AT1', 'AT2', 'TELEF', 'CKS'] as $key) {
            $arrayHelpers->moveToSubarray($fromDB, $key, ['DBF', 'contacts', $key]);
        }
        foreach (['R_CLOSE', 'PRIM1', 'PRIM2', 'PRIM3'] as $key) {
            $arrayHelpers->moveToSubarray($fromDB, $key, ['DBF', 'removal', $key]);
        }
        foreach (['DATE_CH', 'VKEY', 'VKEYDEL', 'BVKEY', 'FVKEY', 'RKC', 'SROK', 'NEWKS', 'OKPO', 'PERMFO'] as $key) {
            $arrayHelpers->moveToSubarray($fromDB, $key, ['DBF', 'misc', $key]);
        }
        #If RKC equals headquarters - remove it. For newer entries, they were essentially replaced
        if ($fromDB['DBF']['misc']['RKC'] === $fromDB['PrntBIC']) {
            $fromDB['DBF']['misc']['RKC'] = NULL;
        }

        #Convert array to properties
        $this->arrayToProperties($fromDB);
    }

    #Function to get list of all predecessors (direct or not)
    /**
     * @throws \Exception
     */
    private function predecessors(string $vkey): array
    {
        $banks = $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `VKEY` FROM `'.self::dbPrefix.'list` WHERE `VKEYDEL` = :BIC ORDER BY `NameP`', [':BIC'=>$vkey]);
        if (empty($banks)) {
            $banks = [];
        } else {
            foreach ($banks as $key=>$bank) {
                $banks[$key]['id'] = $this->padBic($bank['id']);
                $predecessor = $this->predecessors($bank['VKEY']);
                if (!empty($predecessor)) {
                    $banks = array_merge($banks, $predecessor);
                }
            }
        }
        return $banks;
    }

    #Function to get all successors (each as a chain)
    /**
     * @throws \Exception
     */
    private function successors(string $vkey): array
    {
        #Get initial list
        $bank = $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `VKEYDEL`, `VKEY`, `DateOut` FROM `'.self::dbPrefix.'list` WHERE `VKEY` = :BIC ORDER BY `NameP`', [':BIC'=>$vkey]);
        if (empty($bank)) {
            $bank = [];
        } else {
            #Get successors for each successor
            foreach ($bank as $key=>$item) {
                $bank[$key]['id'] = $this->padBic($item['id']);
                if (!empty($item[0]['VKEYDEL']) && $item[0]['VKEYDEL'] !== $vkey && $bank[0]['VKEYDEL'] !== $bank[0]['VKEY']) {
                    $bank[$key] = array_merge($item, $this->successors($item[0]['id']));
                }
            }
        }
        return $bank;
    }

    #Function to get all RKCs for a bank as a chain
    /**
     * @throws \Exception
     */
    private function rkcChain(string $bic): array
    {
        $banks = [];
        #Get initial list
        $bank = $this->dbController->selectRow('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `'.self::dbPrefix.'list` WHERE `BIC` = :BIC', [':BIC'=>$bic]);
        if (empty($bank)) {
            return $banks;
        } else {
            $banks[] = $bank;
            $bank['id'] = $this->padBic($bank['id']);
            if (!empty($bank['RKC'])) {
                $bank['RKC'] = $this->padBic($bank['RKC']);
            }
            if (!empty($bank['PrntBIC'])) {
                $bank['PrntBIC'] = $this->padBic($bank['PrntBIC']);
            }
            #Get RKC for RKC
            if (!empty($bank['RKC']) && $bank['RKC'] !== $bic && $bank['RKC'] !== $bank['id']) {
                $banks = array_merge($banks, $this->rkcChain($bank['RKC']));
            }
        }
        return $banks;
    }

    #Function to get authorized branches as a chain
    /**
     * @throws \Exception
     */
    private function bicUf(string $bic): array
    {
        $banks = [];
        #Get initial list
        $bank = $this->dbController->selectRow('SELECT \'bic\' as `type`,`BIC` as `id`,`NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `'.self::dbPrefix.'list` WHERE `BIC` = :BIC', [':BIC'=>$bic]);
        if (empty($bank)) {
            return $banks;
        } else {
            $banks[] = $bank;
            $bank['id'] = $this->padBic($bank['id']);
            if (!empty($bank['PrntBIC'])) {
                $bank['PrntBIC'] = $this->padBic($bank['PrntBIC']);
            }
            if (!empty($bank['RKC'])) {
                $bank['RKC'] = $this->padBic($bank['RKC']);
            }
            #Get authorized branch of authorized branch
            if (!empty($bank['PrntBIC']) && $bank['PrntBIC'] !== $bic && isset($bank['id']) && $bank['PrntBIC'] !== $bank['id']) {
                $banks = array_merge($banks, $this->bicUf($bank['PrntBIC']));
            }
        }
        return $banks;
    }

    #Function to get all branches of a bank
    /**
     * @throws \Exception
     */
    private function getBranches(string $bic): array
    {
        $banks = $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `BIC`, `NameP` as `name`, `DateOut` FROM `'.self::dbPrefix.'list` WHERE `PrntBIC` = :BIC ORDER BY `NameP`;', [':BIC'=>$bic]);
        if (empty($banks)) {
            $banks = [];
        } else {
            foreach ($banks as $key=>$bank) {
                $banks[$key]['id'] = $this->padBic($bank['id']);
                $predecessor = $this->getBranches($bank['id']);
                if (!empty($predecessor)) {
                    $banks = array_merge($banks, $predecessor);
                }
            }
        }
        return $banks;
    }

    #Function to format list of phones
    private function phoneList(string $phoneString): array
    {
        #Remove empty brackets
        $phoneString = str_replace('()', '', $phoneString);
        #Remvoe pager notation (obsolete)
        $phoneString = str_replace('ПЕЙД', '', $phoneString);
        #Update Moscow code
        $phoneString = str_replace('(095)', '(495)', $phoneString);
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
            $dobs = preg_replace('/[^0-9,]/', '', $dob[1]);
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
        foreach ($phones as $key=>$phone) {
            if (!preg_match('/\((\d*)\)/', $phone)) {
                $phone = $code.$phone;
            } else {
                $phone = '+7 '.$phone;
                if (!preg_match('/\) /', $phone)) {
                    $phone = preg_replace('/\)/', ') ', $phone);
                }
            }
            $phones[$key] = ['phone'=>$phone,'url'=>preg_replace('/[^0-9+]/', '', $phone)];
        }
        return ['phones'=>$phones,'dob'=>$dobs];
    }

    #Pad BICs with zeros
    private function padBic(string $bic): string
    {
        return str_pad($bic, 9, '0', STR_PAD_LEFT);
    }
}

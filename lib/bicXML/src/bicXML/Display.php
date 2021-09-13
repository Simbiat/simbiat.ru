<?php
declare(strict_types=1);
namespace Simbiat\bicXML;

use Simbiat\Database\Controller;

class Display
{
    const dbPrefix = 'bic__';

    #Function to return current data about the bank
    /**
     * @throws \Exception
     */
    public function getCurrent(string $BIC): array
    {
        #Get general data
        $bicDetails = (new Controller)->selectRow('SELECT `biclist`.`VKEY`, `VKEYDEL`, `BVKEY`, `FVKEY`, `Adr`, `AT1`, `AT2`, `CKS`, `DATE_CH`, `DateIn`, `DateOut`, `Updated`, `Ind`, `bic__srvcs`.`Description` AS `Srvcs`, `NameP`, `NAMEMAXB`, `NEWKS`, biclist.`BIC`, `PrntBIC`, `SWIFT_NAME`, `Nnp`, `OKPO`, `PERMFO`, `bic__pzn`.`NAME` AS `PtType`, `bic__rclose`.`NAMECLOSE` AS `R_CLOSE`, `RegN`, `bic__reg`.`NAME` AS `Rgn`, `bic__reg`.`CENTER`, `RKC`, `SROK`, `TELEF`, `Tnp`, `PRIM1`, `PRIM2`, `PRIM3` FROM `bic__list` biclist
                LEFT JOIN `bic__reg` ON `bic__reg`.`RGN` = biclist.`Rgn`
                LEFT JOIN `bic__pzn` ON `bic__pzn`.`PtType` = biclist.`PtType`
                LEFT JOIN `bic__rclose` ON `bic__rclose`.`R_CLOSE` = biclist.`R_CLOSE`
                LEFT JOIN `bic__srvcs` ON `bic__srvcs`.`Srvcs` = biclist.`Srvcs`
                WHERE biclist.`BIC` = :BIC', [':BIC'=>$BIC]);
        if (empty($bicDetails)) {
            return [];
        } else {
            #Generating address from different fields
            $bicDetails['Adr'] = (!empty($bicDetails['Ind']) ? $bicDetails['Ind'].' ' : '').(!empty($bicDetails['Tnp']) ? $bicDetails['Tnp'].' ' : '').(!empty($bicDetails['Nnp']) ? $bicDetails['Nnp'].', ' : '').$bicDetails['Adr'];
            #Get list of phones
            if (!empty($bicDetails['TELEF'])) {
                $bicDetails['TELEF'] = $this->phoneList($bicDetails['TELEF']);
            } else {
                $bicDetails['TELEF'] = [];
            }
            #If RKC=BIC it means, that current bank is RKC and does not have bank above it
            if ($bicDetails['RKC'] == $bicDetails['BIC']) {
                $bicDetails['RKC'] = '';
            }
            #If we have an RKC - get the whole chain of RKCs
            if (!empty($bicDetails['RKC'])) {$bicDetails['RKC'] = $this->rkcChain($bicDetails['RKC']);}
            #Get authorized branch
            if (!empty($bicDetails['PrntBIC'])) {$bicDetails['PrntBIC'] = $this->bicUf($bicDetails['PrntBIC']);}
            #Get all branches of the bank (if any)
            $bicDetails['filials'] = $this->filials($bicDetails['BIC']);
            #Get the chain of predecessors (if any)
            $bicDetails['predecessors'] = $this->predecessors($bicDetails['VKEY']);
            #Get the chain of successors (if any)
            $bicDetails['successors'] = (empty($bicDetails['VKEYDEL']) ? [] : $this->successors($bicDetails['VKEYDEL']));
            return $bicDetails;
        }
    }

    #Function to get basic statistics
    /**
     * @throws \Exception
     */
    public function bicDate(): string
    {
        return (new Controller)->selectValue('SELECT `value` FROM `'.self::dbPrefix.'settings` WHERE `setting`=\'date\';');
    }

    #Function to get list of all predecessors (each as a chain)

    /**
     * @throws \Exception
     */
    private function predecessors(string $vkey): array
    {
        #Get initial list
        $bank = (new Controller)->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `VKEYDEL` = :BIC ORDER BY `NameP`', [':BIC'=>$vkey]);
        if (empty($bank)) {
            $bank = array();
        } else {
            foreach ($bank as $key=>$item) {
                #Check for predecessors of predecessor
                $next = $this->predecessors($item['id']);
                if (!empty($next)) {
                    #If predecessor has a predecessor as well - get its predecessors
                    if (count($next) == 1) {
                        if (!empty($next[0][0]) && is_array($next[0][0])) {
                            $bank[$key] = [];
                            foreach ($next[0] as $nextI) {
                                $bank[$key][] = $nextI;
                            }
                            $bank[$key][] = $item;
                        } else {
                            $bank[$key] = [$next[0], $item];
                        }
                    }
                }
            }
        }
        return $bank;
    }

    #Function to get all successors (each as a chain)

    /**
     * @throws \Exception
     */
    private function successors(string $vkey): array
    {
        #Get initial list
        $bank = (new Controller)->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `VKEYDEL`, `DateOut` FROM `bic__list` WHERE `VKEY` = :BIC ORDER BY `NameP`', [':BIC'=>$vkey]);
        if (empty($bank)) {
            $bank = [];
        } else {
            #Get successors for each successor
            foreach ($bank as $key=>$item) {
                if (!empty($item[0]['VKEYDEL']) && $item[0]['VKEYDEL'] != $vkey && $bank[0]['VKEYDEL'] != $bank[0]['id']) {
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
        #Get initial list
        $bank = (new Controller)->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `BIC`, `RKC`, `DateOut` FROM `bic__list` WHERE `BIC` = :BIC AND `DateOut` IS NULL LIMIT 1', [':BIC'=>$bic]);
        if (empty($bank)) {
            $bank = [];
        } else {
            #Get RKC for RKC
            if (!empty($bank[0]['RKC']) && $bank[0]['RKC'] != $bic && $bank[0]['RKC'] != $bank[0]['BIC']) {
                $bank = array_merge($bank, $this->rkcChain($bank[0]['RKC']));
            }
        }
        return $bank;
    }

    #Function to get authorized branches as a chain
    /**
     * @throws \Exception
     */
    private function bicUf(string $bic): array
    {
        #Get initial list
        $bank = (new Controller)->selectAll('SELECT \'bic\' as `type`,`BIC` as `id`,`NameP` as `name`, `DateOut`, `PrntBIC` FROM `bic__list` WHERE `BIC` = :BIC LIMIT 1', [':BIC'=>$bic]);
        if (empty($bank)) {
            $bank = [];
        } else {
            #Get authorized branch of authorized branch
            if (!empty($bank[0]['PrntBIC']) && $bank[0]['PrntBIC'] != $bic && isset($bank[0]['BIC']) && $bank[0]['PrntBIC'] != $bank[0]['BIC']) {
                $bank = array_merge($bank, $this->bicUf($bank[0]['PrntBIC']));
            }
        }
        return $bank;
    }

    #Function to get all branches of a bank
    /**
     * @throws \Exception
     */
    private function filials(string $bic): array
    {
        $bank = (new Controller)->selectAll('SELECT \'bic\' as `type`, biclist.`BIC` as `id`, biclist.`BIC`, biclist.`NameP` as `name`, biclist.`DateOut` FROM `bic__list` biclist LEFT JOIN `bic__list` bicco ON biclist.`BIC` = bicco.`PrntBIC` WHERE biclist.`PrntBIC` = :BIC ORDER BY biclist.`NameP`', [':BIC'=>$bic]);
        if (empty($bank)) {
            $bank = [];
        }
        return $bank;
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
}

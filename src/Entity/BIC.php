<?php

declare(strict_types=1);

// TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")

namespace App\Entity;

use App\Enum\BIC\AccRstrType;
use App\Enum\BIC\RstrType;
use Simbiat\ArrayHelpers\Editors;
use Simbiat\ArrayHelpers\Sorters;
use Simbiat\Database\Query;

/**
 * Class representing a Bank Identification Code (BIC).
 *
 * @api TODO: most likely will need to remove once entities migrate to Doctrine. For now this is mostly to silence Psalm.
 */
final class BIC extends Entity
{
    /**
     * @var array List of accounts
     */
    public array $accounts = [];

    /**
     * @var string|null Address
     */
    public ?string $Adr = null;

    /**
     * @var string Bank code of the entity
     */
    public string $BIC;

    /**
     * @var array List of branches if any
     */
    public array $branches = [];

    /**
     * @var string|null Processing center
     */
    public ?string $CENTER = null;

    /**
     * @var string|null Country code
     */
    public ?string $CntrCd = null;

    /**
     * @var string|null Date added
     */
    public ?string $DateIn = null;

    /**
     * @var string|null Date removed from the official library
     */
    public ?string $DateOut = null;

    /**
     * @var array Old data from DBF files
     */
    public array $DBF = [];

    /**
     * @var string|null English name of the entity
     */
    public ?string $EnglName = null;

    /**
     * @var string|null Postal index
     */
    public ?string $Ind = null;

    /**
     * @var string Name of the entity
     */
    public string $NameP;

    /**
     * @var string|null Name of the location
     */
    public ?string $Nnp = null;

    /**
     * @var string|null Old BIC, in case it was used by several organizations
     */
    public ?string $OLD_NEWNUM = null;

    /**
     * @var array|string|null Bank code of parent entity
     */
    public array|string|null $PrntBIC = null;

    /**
     * @var string|null Type of entity
     */
    public ?string $PtType = null;

    /**
     * @var string|null Registration number
     */
    public ?string $RegN = null;

    /**
     * @var array Restrictions on the whole organization
     */
    public array $restrictions = [];

    /**
     * @var string|null Location region
     */
    public ?string $Rgn = null;

    /**
     * @var array List of organizations using the same address
     */
    public array $same_address = [];

    /**
     * @var array List of organizations with the same BIC used in the past
     */
    public array $same_bic = [];

    /**
     * @var int Number of entities serviced by entity
     */
    public int $service_for = 0;

    /**
     * @var string|null Types of services provided
     */
    public ?string $Srvcs = null;

    /**
     * @var array List of SWIFT codes if any
     */
    public array $SWIFTs = [];

    /**
     * @var string|null Type of location
     */
    public ?string $Tnp = null;

    /**
     * @var string|null UID for electronic messages used by the organization
     */
    public ?string $UID = null;

    /**
     * @var string|null Date of the latest update
     */
    public ?string $Updated = null;

    /**
     * @var bool|int|string Whether the entity is active or not
     */
    public bool|int|string $XchType = false;

    /**
     * Get BIC data from DB.
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException|\UnexpectedValueException
     */
    #[\Override]
    protected function getFromDB(): array
    {
        return Query::query('SELECT `biclist`.`VKEY`, `VKEYDEL`, `BVKEY`, `FVKEY`, `OLD_NEWNUM`, `EnglName`, `XchType`, `UID`, `CntrCd`, `Adr`, `AT1`, `AT2`, `CKS`, `DATE_CH`, `DateIn`, `DateOut`, `Updated`, `Ind`, `bic__srvcs`.`Description` AS `Srvcs`, `NameP`, `NAMEMAXB`, `NEWKS`, biclist.`BIC`, `PrntBIC`, `SWIFT_NAME`, `Nnp`, `OKPO`, `PERMFO`, `bic__pzn`.`NAME` AS `PtType`, `bic__rclose`.`NAMECLOSE` AS `R_CLOSE`, `RegN`, `bic__reg`.`NAME` AS `Rgn`, `bic__reg`.`CENTER`, `RKC`, `SROK`, `TELEF`, `Tnp`, `PRIM1`, `PRIM2`, `PRIM3` FROM `bic__list` biclist
            LEFT JOIN `bic__reg` ON `bic__reg`.`RGN` = `biclist`.`Rgn`
            LEFT JOIN `bic__pzn` ON `bic__pzn`.`PtType` = `biclist`.`PtType`
            LEFT JOIN `bic__rclose` ON `bic__rclose`.`R_CLOSE` = `biclist`.`R_CLOSE`
            LEFT JOIN `bic__srvcs` ON `bic__srvcs`.`Srvcs` = `biclist`.`Srvcs`
            WHERE biclist.`BIC` = :BIC', [':BIC' => $this->id], return: 'row');
    }

    /**
     * Function to return current data about the bank.
     *
     * @param array $from_db
     *
     * @throws \Exception
     */
    #[\Override]
    protected function process(array $from_db): void
    {
        // Pad stuff
        $from_db['BIC'] = self::padBic((string) $from_db['BIC']);
        if (!empty($from_db['PrntBIC'])) {
            $from_db['PrntBIC'] = self::padBic((string) $from_db['PrntBIC']);
        }

        if (!empty($from_db['RKC'])) {
            $from_db['RKC'] = self::padBic((string) $from_db['RKC']);
        }

        if (!empty($from_db['OLD_NEWNUM'])) {
            $from_db['OLD_NEWNUM'] = self::padBic((string) $from_db['OLD_NEWNUM']);
        }

        // Get authorized branch
        if (!empty($from_db['PrntBIC'])) {
            $from_db['PrntBIC'] = $this->bicUf($from_db['PrntBIC']);
        }

        // Get all branches of the bank (if any)
        $from_db['branches'] = $this->getBranches($from_db['BIC']);
        $from_db['branches'] = Sorters::multiArrSort($from_db['branches'], 'name');
        // Get SWIFT codes
        $from_db['SWIFTs'] = Query::query('SELECT `SWBIC`, `DefaultSWBIC`, `DateIn`, `DateOut` FROM `bic__swift` WHERE `BIC`=:BIC ORDER BY `DefaultSWBIC` DESC, `DateOut` DESC', [':BIC' => $this->id], return: 'all');
        // Get restrictions for BIC
        $from_db['restrictions'] = Query::query("SELECT `bic__bic_rstr`.`Rstr` as `name`, `RstrDate` as `start_time`, `DateOut` as `end_time`, 'bic' as `type` FROM `bic__bic_rstr` WHERE `BIC`=:BIC ORDER BY `RstrDate` DESC;", [':BIC' => $this->id], return: 'all');
        foreach ($from_db['restrictions'] as $key => $restriction) {
            $from_db['restrictions'][$key]['description'] = RstrType::{$restriction['name']}->value;
        }

        // Get accounts
        $from_db['accounts'] = Query::query(
            'SELECT `Account`, `bic__acc_type`.`Description` as `AccountType`, `CK`, `DateIn`, `DateOut`, `AccountCBRBIC` FROM `bic__accounts`
                LEFT JOIN `bic__acc_type` ON `bic__accounts`.`RegulationAccountType`=`bic__acc_type`.`RegulationAccountType` WHERE `bic__accounts`.`BIC`=:BIC',
            [':BIC' => $this->id],
            return: 'all',
        );
        foreach ($from_db['accounts'] as $account) {
            // Get restrictions
            $account_restrictions = Query::query("SELECT `bic__acc_rstr`.`AccRstr` as `name`, `AccRstrDate` as `start_time`, `DateOut` as `end_time`, `SuccessorBIC`, 'account' as `type`, `account` FROM `bic__acc_rstr` WHERE `account`=:account ORDER BY `AccRstrDate` DESC;", [':account' => $account['Account']], return: 'all');
            // Get successor details for restrictions
            foreach ($account_restrictions as $key_rstr => $restriction) {
                $account_restrictions[$key_rstr]['description'] = AccRstrType::{$restriction['name']}->value;
                if (!empty($restriction['SuccessorBIC'])) {
                    $account_restrictions[$key_rstr]['SuccessorBIC'] = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `BIC`=:BIC;", [':BIC' => self::padBic((string) $restriction['SuccessorBIC'])], return: 'row');
                }

                $from_db['restrictions'][] = $account_restrictions[$key_rstr];
            }
        }

        // Count banks that are serviced by this one
        $from_db['service_for'] = Query::query('SELECT COUNT(*) AS `count` FROM `bic__accounts` WHERE `AccountCBRBIC`=:BIC', [':BIC' => $this->id], return: 'count');
        // Get a list of banks that used the same BIC
        $from_db['same_bic'] = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `OLD_NEWNUM`=:NEWNUM AND `BIC`<>:BIC;", [':NEWNUM' => $from_db['OLD_NEWNUM'] ?? $from_db['BIC'], ':BIC' => $from_db['BIC']], return: 'all');
        // Get a list of banks on the same address
        $from_db['same_address'] = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `Adr`=:Adr AND `BIC`<>:BIC;", [':Adr' => $from_db['Adr'], ':BIC' => $this->id], return: 'all');

        // Old DBF data processing
        // Gets a list of phones
        $from_db['TELEF'] = !empty($from_db['TELEF'])
            ? self::phoneList($from_db['TELEF'])
            : [];
        // If RKC is the same as BIC, it means that the current bank is RKC and does not have a bank above it
        if ($from_db['RKC'] === $from_db['BIC']) {
            $from_db['RKC'] = null;
        }

        // Chains based on DBF data
        if (!empty($from_db['RKC'])) {
            $from_db['RKC'] = $this->rkcChain($from_db['RKC']);
        }

        // Get the chain of predecessors (if any) based on DBF data
        $from_db['DBF']['predecessors'] = (empty($from_db['VKEY']) ? [] : self::predecessors($from_db['VKEY']));
        $from_db['DBF']['predecessors'] = Sorters::multiArrSort($from_db['DBF']['predecessors'], 'name');
        // Get the chain of successors (if any) based on DBF data
        $from_db['DBF']['successors'] = (empty($from_db['VKEYDEL']) ? [] : self::successors($from_db['VKEYDEL']));
        // Moving the DBF-related values around
        foreach (['NAMEMAXB', 'NAMEN', 'SWIFT_NAME'] as $key) {
            Editors::moveToSubarray($from_db, $key, ['DBF', 'names', $key]);
        }

        foreach (['AT1', 'AT2', 'TELEF', 'CKS'] as $key) {
            Editors::moveToSubarray($from_db, $key, ['DBF', 'contacts', $key]);
        }

        foreach (['R_CLOSE', 'PRIM1', 'PRIM2', 'PRIM3'] as $key) {
            Editors::moveToSubarray($from_db, $key, ['DBF', 'removal', $key]);
        }

        foreach (['DATE_CH', 'VKEY', 'VKEYDEL', 'BVKEY', 'FVKEY', 'RKC', 'SROK', 'NEWKS', 'OKPO', 'PERMFO'] as $key) {
            Editors::moveToSubarray($from_db, $key, ['DBF', 'misc', $key]);
        }

        // If RKC equals headquarters - remove it. For newer entries, they were essentially replaced
        if ($from_db['DBF']['misc']['RKC'] === $from_db['PrntBIC']) {
            $from_db['DBF']['misc']['RKC'] = null;
        }

        $this->OLD_NEWNUM = $from_db['OLD_NEWNUM'];
        $this->EnglName = $from_db['EnglName'];
        $this->XchType = $from_db['XchType'];
        $this->UID = $from_db['UID'];
        $this->CntrCd = $from_db['CntrCd'];
        $this->Adr = $from_db['Adr'];
        $this->DateIn = $from_db['DateIn'];
        $this->DateOut = $from_db['DateOut'];
        $this->Updated = $from_db['Updated'];
        $this->Ind = $from_db['Ind'];
        $this->Srvcs = $from_db['Srvcs'];
        $this->NameP = $from_db['NameP'];
        $this->BIC = $from_db['BIC'];
        $this->PrntBIC = $from_db['PrntBIC'];
        $this->Nnp = $from_db['Nnp'];
        $this->PtType = $from_db['PtType'];
        $this->RegN = $from_db['RegN'];
        $this->Rgn = $from_db['Rgn'];
        $this->CENTER = $from_db['CENTER'];
        $this->Tnp = $from_db['Tnp'];
        $this->branches = $from_db['branches'];
        $this->SWIFTs = $from_db['SWIFTs'];
        $this->restrictions = $from_db['restrictions'];
        $this->accounts = $from_db['accounts'];
        $this->service_for = $from_db['service_for'];
        $this->same_bic = $from_db['same_bic'];
        $this->same_address = $from_db['same_address'];
        $this->DBF = $from_db['DBF'];
    }

    /**
     * Function to get authorized branches as a chain.
     *
     * @param string $bic
     *
     * @return array
     *
     * @throws \Exception
     */
    private function bicUf(string $bic): array
    {
        $banks = [];
        // Get the initial list
        $bank = Query::query("SELECT 'bic' as `type`,`BIC` as `id`,`NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `bic__list` WHERE `BIC` = :BIC", [':BIC' => $bic], return: 'row');
        if (empty($bank)) {
            return $banks;
        }

        $bank['id'] = self::padBic((string) $bank['id']);
        if (!empty($bank['PrntBIC'])) {
            $bank['PrntBIC'] = self::padBic((string) $bank['PrntBIC']);
        }

        if (!empty($bank['RKC'])) {
            $bank['RKC'] = self::padBic((string) $bank['RKC']);
        }

        $banks[] = $bank;
        // Get authorized branch of authorized branch
        if (
            !empty($bank['PrntBIC'])
            && $bank['PrntBIC'] !== $bic
            && !empty($bank['id'])
            && $bank['PrntBIC'] !== $bank['id']
        ) {
            return \array_merge($banks, $this->bicUf($bank['PrntBIC']));
        }

        return $banks;
    }

    /**
     * Function to get all branches of a bank.
     *
     * @param string $bic
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getBranches(string $bic): array
    {
        $banks = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `BIC`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `PrntBIC` = :BIC ORDER BY `NameP`;", [':BIC' => $bic], return: 'all');
        $branches = [$banks];
        if ($banks !== false) {
            foreach ($banks as $key => $bank) {
                $banks[$key]['id'] = self::padBic((string) $bank['id']);
                $branch = $this->getBranches($bank['id']);
                if (\count($branch) !== 0) {
                    $branches[] = $branch;
                }
            }

            return \array_merge(...$branches);
        }

        return [];
    }

    /**
     * Pad BICs with zeros.
     *
     * @param string $bic
     *
     * @return string
     */
    private static function padBic(string $bic): string
    {
        return \mb_str_pad($bic, 9, '0', \STR_PAD_LEFT, 'UTF-8');
    }

    /**
     * Function to format a list of phones.
     *
     * @param string $phone_string
     *
     * @return array{phones: array<int, array{phone: string|null, url: string|null}>, dob: string}
     */
    private static function phoneList(string $phone_string): array
    {
        // Remove empty brackets
        // Remove pager notation (obsolete)
        // Update Moscow code
        $phone_string = \str_replace(['()', 'ПЕЙД', '(095)'], ['', '', '(495)'], $phone_string);
        // Attempt to get the additional number (to be entered after you've dialed-in)
        $dob = \explode(',ДОБ.', $phone_string);
        if (empty($dob[1])) {
            $dob = \explode(',ДБ.', $phone_string);
            if (empty($dob[1])) {
                $dob = \explode('(ДОБ.', $phone_string);
                if (empty($dob[1])) {
                    $dob = \explode(' ДОБ.', $phone_string);
                    if (empty($dob[1])) {
                        $dob = \explode('ДОБ', $phone_string);
                        if (empty($dob[1])) {
                            $dob = \explode(' код ', $phone_string);
                            if (empty($dob[1])) {
                                $dob = \explode(',АБ.', $phone_string);
                                if (empty($dob[1])) {
                                    $dob = \explode(',Д.', $phone_string);
                                    if (empty($dob[1])) {
                                        $dob = \explode('(Д.', $phone_string);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Check if there are additional numbers
        if (empty($dob[1])) {
            $dobs = '';
        } else {
            // Remove all letters from the additional number
            $dobs = \preg_replace('/[^\d,]/', '', $dob[1]);
            // Replace ','. To be honest, not sure why I did this through explode/implode, but I think this helped with removing empty values
            $dobs = \explode(',', $dobs);
            $dobs = \implode(' или ', $dobs);
        }

        // Get actual phones
        $phones_to_parse = \explode(',', $dob[0]);
        $phones = [];
        // Attempting to sanitize the phone numbers to use +7 code only
        \preg_match('/\((\d*)\)/', $phones_to_parse[0], $code);
        $code = empty($code[1])
            ? '+7 '
            : '+7 ('.$code[1].') ';
        foreach ($phones_to_parse as $key => $phone) {
            if (\preg_match('/\((\d*)\)/', $phone)) {
                $phone = '+7 '.$phone;
                if (!\preg_match('/\) /', $phone)) {
                    $phone = \preg_replace('/\)/', ') ', $phone);
                }
            } else {
                $phone = $code.$phone;
            }

            $phones[$key] = ['phone' => $phone, 'url' => \preg_replace('/[^\d+]/', '', $phone)];
        }

        return ['phones' => $phones, 'dob' => $dobs];
    }

    /**
     * Function to get a list of all predecessors (direct or not).
     *
     * @param string $vkey
     *
     * @return array
     *
     * @throws \Exception
     */
    private static function predecessors(string $vkey): array
    {
        $banks = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `VKEY` FROM `bic__list` WHERE `VKEYDEL` = :BIC ORDER BY `NameP`", [':BIC' => $vkey], return: 'all');
        $predecessors = [$banks];
        if ($banks !== false) {
            foreach ($banks as $key => $bank) {
                $banks[$key]['id'] = self::padBic((string) $bank['id']);
                $predecessor = self::predecessors($bank['VKEY']);
                if (\count($predecessor) !== 0) {
                    $predecessors[] = $predecessor;
                }
            }

            return \array_merge(...$predecessors);
        }

        return [];
    }

    /**
     * Function to get all RKCs for a bank as a chain.
     *
     * @param string $bic
     *
     * @return array
     *
     * @throws \Exception
     */
    private function rkcChain(string $bic): array
    {
        $banks = [];
        // Get the initial list
        $bank = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`, `RKC`, `PrntBIC` FROM `bic__list` WHERE `BIC` = :BIC", [':BIC' => $bic], return: 'row');
        if (
            $bank === false
            || $bank === []
        ) {
            return [];
        }

        $bank['id'] = self::padBic((string) $bank['id']);
        if (!empty($bank['RKC'])) {
            $bank['RKC'] = self::padBic((string) $bank['RKC']);
        }

        if (!empty($bank['PrntBIC'])) {
            $bank['PrntBIC'] = self::padBic((string) $bank['PrntBIC']);
        }

        $banks[] = $bank;
        // Get RKC for RKC
        if (
            !empty($bank['RKC'])
            && $bank['RKC'] !== $bic
            && $bank['RKC'] !== $bank['id']
        ) {
            return \array_merge($banks, $this->rkcChain($bank['RKC']));
        }

        return $banks;
    }

    /**
     * Function to get all successors (each as a chain).
     *
     * @param string $vkey
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws \Exception
     */
    private static function successors(string $vkey): array
    {
        // Get the initial list
        $bank = Query::query("SELECT 'bic' as `type`, `BIC` as `id`, `NameP` as `name`, `VKEYDEL`, `VKEY`, `DateOut` FROM `bic__list` WHERE `VKEY` = :BIC ORDER BY `NameP`", [':BIC' => $vkey], return: 'all');
        if ($bank !== false) {
            // Get successors for each successor
            foreach ($bank as $key => $item) {
                $bank[$key]['id'] = self::padBic((string) $item['id']);
                if (
                    !empty($item[0]['VKEYDEL'])
                    && $item[0]['VKEYDEL'] !== $vkey
                    && $bank[0]['VKEYDEL'] !== $bank[0]['VKEY']
                ) {
                    $bank[$key] = \array_merge($item, self::successors($item[0]['id']));
                }
            }
        } else {
            $bank = [];
        }

        return $bank;
    }
}

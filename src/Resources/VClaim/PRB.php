<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Exceptions\TrustmarkException;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\Transporters\HttpTransporter;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Respect\Validation\Validator as v;

final class PRB extends BaseVClaim implements VClaimContract
{
    private DecryptorContract $decryptor;

    public function __construct(private readonly HttpTransporter $transporter)
    {
        $this->decryptor = $this->createDecryptor($this->transporter->getConfig('consId'), $this->transporter->getConfig('secretKey'));
    }

    public function getServiceName(): string
    {
        return 'prb';
    }

    /**
     * Pencarian data PRB berdasarkan nomor SRB
     *
     * @param  string  $srbNumber  Nomor SRB
     * @param  string  $noSep  NOmor SEP yang digunakan untuk membuat SRB
     * @return mixed
     */
    public function listBySRB(string $srbNumber, string $noSep)
    {
        // Validate Data
        PackageValidator::validate([
            'srbNumber' => $srbNumber,
            'noSep' => $noSep,
        ],
            [
                'srbNumber' => 'required|string',
                'noSep' => 'required|string|size:19',
            ]);

        // Write Format URI
        $formatUri = '%s/%s/nosep/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $srbNumber,
            $noSep,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Pencarian data PRB berdasarkan interval tanggal tertentu.
     *
     * @param  string  $startDate  Tanggal mulai. Kurang dari hari ini.
     * @param  string  $endDate  Tanggal akhir. Lebih dari tanggal mulai.
     * @return mixed
     */
    public function listByDate(string $startDate, string $endDate)
    {
        // Validate Data
        PackageValidator::validate([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ],
            [
                'startDate' => 'required|string|date_format:Y-m-d|before_or_equal:today',
                'endDate' => 'required|string|date_format:Y-m-d|after_or_equal:startDate',
            ]);

        // Write Format URI
        $formatUri = '%s/tglMulai/%s/tglAkhir/%s';

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [
            $this->getServiceName(),
            $startDate,
            $endDate,
        ]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function insert(array $data)
    {
        $rules = $this->getValidationRules(['insert' => $data]);
        $this->validate($data, $rules);

        $uri = '%s/insert';

        $body = $this->createBody('insert', $data);

        $payload = Payload::insert($uri, [$this->getServiceName()], $body);

        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function update(array $data)
    {
        $rules = $this->getValidationRules(['update' => $data]);
        $this->validate($data, $rules);

        $uri = '%s/Update';

        $body = $this->createBody('update', $data);

        $payload = Payload::update($uri, [$this->getServiceName()], $body);

        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function delete(array $data)
    {
        $rules = $this->getValidationRules(['delete' => $data]);
        $this->validate($data, $rules);

        $uri = '%s/Delete';

        $body = $this->createBody('delete', $data);

        $payload = Payload::delete($uri, [$this->getServiceName()], $body);

        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    /**
     * Get validation rules for the provided keys.
     */
    public function getValidationRules(array $keys): array
    {
        $sharedRules = [
            'noRujukan' => v::stringType()->length(15, 20)->setName('Nomor Rujukan (noRujukan)'),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'kodePPK' => v::stringType()->length(8, 8)->setName('Kode PPK'),
            'tanggalAwal' => v::date('Y-m-d'),
            'tanggalAkhir' => v::date('Y-m-d'),
            'noSep' => v::stringType()->length(19, 19, true),
            'noSrb' => v::stringType()->length(8, 16, true),
            'alamat' => v::stringType(),
            'email' => v::email(),
            'saran' => v::stringType(),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),
            'user' => v::intType(),
            'catatan' => v::nullable(v::stringType()),
            'diagAwal' => v::stringType(),
            'poliTujuan' => v::stringType(),
            'kodeDPJP' => v::nullable(v::stringType()->length(3, null)),
            'programPRB' => v::stringType(),
            'obatPRB' => v::each(
                v::key('kdObat', v::stringType()->length(3, null))
                    ->key('signa1', v::intType())
                    ->key('signa2', v::intType())
                    ->key('jmlObat', v::intType())
            ),
        ];

        $rules = [
            ...$sharedRules,
            'insertRujukan' => v::key('noSep', $sharedRules['noSep'])
                ->key('noKartu', $sharedRules['noBpjs'])
                ->key('alamat', $sharedRules['alamat'])
                ->key('email', $sharedRules['email'])
                ->key('programPRB', $sharedRules['programPRB'])
                ->key('kodeDPJP', $sharedRules['kodeDPJP'])
                ->key('keterangan', $sharedRules['saran'])
                ->key('saran', $sharedRules['saran'])
                ->key('user', $sharedRules['user'])
                ->key('obat', $sharedRules['obatPRB']),
            'update' => v::key('noSep', $sharedRules['noSep'])
                ->key('noSrb', $sharedRules['noSrb'])
                ->key('alamat', $sharedRules['alamat'])
                ->key('email', $sharedRules['email'])
                ->key('kodeDPJP', $sharedRules['kodeDPJP'])
                ->key('keterangan', $sharedRules['saran'])
                ->key('saran', $sharedRules['saran'])
                ->key('user', $sharedRules['user'])
                ->key('obat', $sharedRules['obatPRB']),
            'delete' => v::key('noSrb', $sharedRules['noSrb'])
                ->v::key('noSep', $sharedRules['noSep'])
                ->key('user', $sharedRules['user']),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }

    private function createBody(string $key, mixed $raw): mixed
    {
        $structures = [
            'insert' => fn ($data) => [
                'request' => [
                    't_prb' => [
                        'noSep' => $data['noSep'],
                        'noKartu' => $data['noKartu'],
                        'alamat' => $data['alamat'],
                        'email' => $data['email'],
                        'programPRB' => $data['programPRB'],
                        'kodeDPJP' => $data['kodeDPJP'],
                        'keterangan' => $data['keterangan'],
                        'saran' => $data['saran'],
                        'user' => $data['user'],
                        'obat' => $data['obat'],
                    ],
                ],
            ],
            'update' => fn ($data) => [
                'request' => [
                    't_prb' => [
                        'noSep' => $data['noSep'],
                        'noSrb' => $data['noSrb'],
                        'alamat' => $data['alamat'],
                        'email' => $data['email'],
                        'kodeDPJP' => $data['kodeDPJP'],
                        'keterangan' => $data['keterangan'],
                        'saran' => $data['saran'],
                        'user' => $data['user'],
                        'obat' => $data['obat'],
                    ],
                ],
            ],
            'delete' => fn ($data) => [
                'request' => [
                    't_prb' => [
                        'noSep' => $data['noSep'],
                        'noSrb' => $data['noSrb'],
                        'user' => $data['user'],
                    ],
                ],
            ],
        ];

        // Throw error if key not exists
        if (! array_key_exists($key, $structures)) {
            throw new TrustmarkException("Key {$key} not exists on structures", 'Validation');
        }

        return $structures[$key]($raw);
    }
}

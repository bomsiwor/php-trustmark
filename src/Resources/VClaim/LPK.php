<?php

namespace Bomsiwor\Trustmark\Resources\VClaim;

use Bomsiwor\Trustmark\Contracts\DecryptorContract;
use Bomsiwor\Trustmark\Contracts\Resources\VClaimContract;
use Bomsiwor\Trustmark\Contracts\TransporterContract;
use Bomsiwor\Trustmark\Core\PackageValidator;
use Bomsiwor\Trustmark\Enums\VClaim\JenisPelayananBPJSEnum;
use Bomsiwor\Trustmark\Exceptions\VClaimException;
use Bomsiwor\Trustmark\Responses\VClaimResponse;
use Bomsiwor\Trustmark\ValueObjects\Transporter\Payload;
use Respect\Validation\Validator as v;

final class LPK extends BaseVClaim implements VClaimContract
{
  public function __construct(private readonly TransporterContract $transporter, private readonly DecryptorContract $decryptor) {}

  public function getServiceName(): string
  {
    return 'LPK';
  }

  public function list(string $tglMasuk, JenisPelayananBPJSEnum $jenisPelayanan)
  {
    // Write Format URI
    $formatUri = sprintf('%s/TglMasuk/%s/JnsPelayanan/%s');

    // Create payload to generate request instance
    $payload = Payload::get($formatUri, [$this->getServiceName(), $tglMasuk, $jenisPelayanan->value]);

    // Send request using transporter
    $result = $this->transporter->sendRequest($payload);

    return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
  }

  public function insert(array $data): mixed
  {
    $rules = $this->getValidationRules(['insert']);
    $this->validate(['insert' => $data], $rules);

    // Construct data based on BPJS API
    $body = $this->createBody('insert', $data);

    // Create request payload
    $uri = '%s/insert';
    $payload = Payload::insert($uri, [$this->getServiceName()], content: $body);

    // Send request
    $result = $this->transporter->sendRequest($payload);

    return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
  }

  public function update(array $data): mixed
  {
    $rules = $this->getValidationRules(['insert']);
    $this->validate(['insert' => $data], $rules);

    // Construct data based on BPJS API
    $body = $this->createBody('insert', $data);

    // Create request payload
    $uri = '%s/update';
    $payload = Payload::insert($uri, [$this->getServiceName()], content: $body);

    // Send request
    $result = $this->transporter->sendRequest($payload);

    return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
  }

  public function getValidationRules(array $keys): array
  {
    $sharedRules = $this->getSharedRules();

    $rules = [
      'insert' => v::key('noSep', $sharedRules['noSep'])
        ->key('tglMasuk', $sharedRules['tanggal'])
        ->key('tglKeluar', $sharedRules['tanggal'])
        ->key('jaminan', v::nullable(v::intType()))
        ->key('poli', $sharedRules['poliTujuan'])
        ->key('ruangRawat', v::nullable(v::intType()))
        ->key('kelasRawat', v::nullable(v::intType()))
        ->key('spesialistik', v::nullable(v::stringType()))
        ->key('caraKeluar', v::nullable(v::intType()))
        ->key('kondisiPulang', v::nullable(v::intType()))
        ->key('tindakLanjut', v::nullable(v::intType()))
        ->key('kodePPKRujukan', v::nullable($sharedRules['kodePPK']))
        ->key('tglKontrolKembali', v::nullable($sharedRules['tanggal']))
        ->key('poliKontrol', v::nullable($sharedRules['poliTujuan']))
        ->key('dpjp', $sharedRules['dpjpLayan'])
        ->key('user', $sharedRules['user'])
        ->key('diagnosis', v::arrayType()
          ->when(
            v::arrayType()->length(1, null),
            v::each(
              v::arrayType()->keySet(
                v::key('kode', v::stringType()),
                v::key('level', v::intType()->in(['1', '2',]))
              )
            )
          ))
        ->key('prosedur', v::arrayType()
          ->when(
            v::arrayType()->length(1, null),
            v::each(
              v::arrayType()->keySet(
                v::key('kode', v::stringType()),
              )
            )
          ))
    ];

    return array_intersect_key($rules, array_flip($keys));
  }

  public function createBody(string $key, mixed $data): mixed
  {
    $builder = [
      'insert' => function ($raw): array {
        return [
          'request' => [
            't_lpk' => [
              'noSep' => $raw['noSep'],
              'tglMasuk' => $raw['tglMasuk'],
              'tglKeluar' => $raw['tglKeluar'],
              'jaminan' => $raw['jaminan'],
              'poli' => [
                'poli' => $raw['poli'],
              ],
              'perawatan' => [
                'ruangRawat' => $raw['ruangRawat'],
                'kelasRawat' => $raw['kelasRawat'],
                'spesialistik' => $raw['spesialistik'],
                'caraKeluar' => $raw['caraKeluar'],
                'kondisiPulang' => $raw['kondisiPulang'],
              ],
              'diagnosa' => $raw['diagnosis'],
              'procedure' => $raw['prosedur'],
              'rencanaTL' => [
                'tindakLanjut' => $raw['tindakLanjut'] ?? "",
                'dirujukKe' => [
                  'kodePPK' => $raw['kodePPKRujukan'] ?? "",
                ],
                'kontrolKembali' => [
                  'tglKontrol' => $raw['tglKontrolKembali'] ?? "",
                  'poli' => $raw['poliKontrol'] ?? "",
                ]
              ],
              'DPJP' => $raw['dpjp'],
              'user' => $raw['user'],
            ]
          ]
        ];
      }
    ];

    // Return null if key not exists
    if (!array_key_exists($key, $builder)) {
      return null;
    }

    public function list(string $tglMasuk, JenisPelayananBPJSEnum $jenisPelayanan)
    {
        // Validate Data
        PackageValidator::validate([
            'tglMasuk' => $tglMasuk,
        ],
            [
                'tglMasuk' => 'required|string|date_format:Y-m-d|before_or_equal:today',
            ]);

        // Write Format URI
        $formatUri = sprintf('%s/TglMasuk/%s/JnsPelayanan/%s');

        // Create payload to generate request instance
        $payload = Payload::get($formatUri, [$this->getServiceName(), $tglMasuk, $jenisPelayanan->value]);

        // Send request using transporter
        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function delete(string $noSep)
    {
        $data = compact('noSep');
        $rules = $this->getValidationRules(array_keys($data));
        $this->validate($data, $rules);

        $uri = '%s/delete';
        // Create body
        $body = $this->createBody('delete', $data);

        $payload = Payload::delete($uri, [$this->getServiceName()], $body);

        $result = $this->transporter->sendRequest($payload);

        return VClaimResponse::from($this->decryptor, $result, $this->transporter->getTimestamp());
    }

    public function getValidationRules(array $keys): array
    {
        $sharedRules = [
            'noRujukan' => v::stringType()->length(15, 20)->setName('Nomor Rujukan (noRujukan)'),
            'noBpjs' => v::stringType()->length(13, 15)->setName('Nomor BPJS'),
            'kodePPK' => v::stringType()->length(8, 8)->setName('Kode PPK'),
            'tglPelayanan' => v::date('Y-m-d')
                ->oneOf(
                    v::lessThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
            'tglRujukan' => v::date('Y-m-d')
                ->oneOf(
                    v::greaterThan((new DateTime)->format('Y-m-d')),
                    v::equals((new DateTime)->format('Y-m-d'))
                ),
            'tanggalAwal' => v::date('Y-m-d'),
            'tanggalAkhir' => v::date('Y-m-d'),
            'noSep' => v::stringType()->length(19, 19, true),
            'bulan' => v::intVal()->between(1, 12),
            'tahun' => v::intVal()->greaterThan(2000),
            'user' => v::stringType()->length(3, null),
            'jnsPelayanan' => v::intType()->in(JenisPelayananBPJSEnum::values()),
            'catatan' => v::nullable(v::stringType()),
            'diagAwal' => v::stringType(),
            'poliTujuan' => v::stringType(),
            'diagnosisMultiple' => v::notEmpty()->each(v::stringType()),
            'procedureMultiple' => v::notEmpty()->each(v::stringType()),
        ];

        $rules = [
            ...$sharedRules,
            'delete' => v::key('noSep', $sharedRules['noSep']),
        ];

        return array_intersect_key($rules, array_flip($keys));
    }

    private function createBody(string $key, mixed $raw): mixed
    {
        $structures = [
            'delete' => fn ($data) => [
                'request' => [
                    't_lpk' => [
                        'noSep' => $data['noSep'],
                    ],
                ],
            ],
        ];

        // Throw error if key not exists
        if (! array_key_exists($key, $structures)) {
            throw new VClaimException("Key {$key} not exists on structures", 'Validation');
        }

        return $structures[$key]($raw);
    }
    return $builder[$key]($data);
  }
}

<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\User;

class CsvExportTest extends TestCase
{
    private $user;

    protected function setUp(): void {
      parent::setUp();

      Http::fake([
        'api.up.com.au/*/transactions*' => Http::response($this->upTransactionsResponse(), 200),
        'api.up.com.au/*' => Http::response($this->upAccountResponse(), 200)
      ]);

      $this->user = User::create([
        'username' => 'csvUser',
        'uptoken' => 'up:yeah:csv',
        'password' => 'abc123'
      ]);
    }

    protected function tearDown(): void {
      $this->user->delete();
      parent::tearDown();
    }

    private function upAccountResponse(): array {
      return [
        'data' => [
          'type' => 'accounts',
          'id' => 'acc-1',
          'attributes' => [
            'displayName' => 'Spending',
            'accountType' => 'TRANSACTIONAL',
            'balance' => [
              'currencyCode' => 'AUD',
              'value' => '100.00',
              'valueInBaseUnits' => 10000
            ],
            'createdAt' => '2020-08-13T17:50:20+10:00'
          ]
        ]
      ];
    }

    private function upTransactionsResponse(): array {
      return [
        'data' => [
          $this->transaction('tx-1', '=HYPERLINK("https://attacker.invalid","click")', '-12.34', -1234),
          $this->transaction('tx-2', 'Coffee', '-4.50', -450),
          $this->transaction('tx-3', 'Quoted "thing"', '25.00', 2500)
        ]
      ];
    }

    private function transaction($id, $description, $value, $baseUnits): array {
      return [
        'type' => 'transactions',
        'id' => $id,
        'attributes' => [
          'status' => 'SETTLED',
          'rawText' => 'RAW',
          'description' => $description,
          'amount' => [
            'currencyCode' => 'AUD',
            'value' => $value,
            'valueInBaseUnits' => $baseUnits
          ],
          'settledAt' => '2020-08-13T17:50:20+10:00',
          'createdAt' => '2020-08-13T17:50:20+10:00'
        ],
        'relationships' => [
          'category' => [
            'data' => [
              'type' => 'categories',
              'id' => 'restaurants-and-cafes'
            ]
          ]
        ]
      ];
    }

    /**
     * A description is whatever the other party typed, so anything a
     * spreadsheet would evaluate has to be defused before export.
     *
     * @return void
     */
    public function testFormulaDescriptionIsNeutralised()
    {
      $response = $this->actingAs($this->user)->get('/accounts/acc-1/csv');

      $response->assertStatus(200);

      $csv = $response->getContent();
      $this->assertStringContainsString('"\'=HYPERLINK', $csv);
      $this->assertStringNotContainsString('"=HYPERLINK', $csv);
    }

    /**
     * Negative amounts start with "-" but are genuine numbers - prefixing
     * them would import every debit as text.
     *
     * @return void
     */
    public function testNegativeAmountsAreNotPrefixed()
    {
      $response = $this->actingAs($this->user)->get('/accounts/acc-1/csv');

      $csv = $response->getContent();
      $this->assertStringContainsString('"-12.34"', $csv);
      $this->assertStringNotContainsString('"\'-12.34"', $csv);
    }

    /**
     * @return void
     */
    public function testQuotesAreDoubled()
    {
      $response = $this->actingAs($this->user)->get('/accounts/acc-1/csv');

      $this->assertStringContainsString('"Quoted ""thing"""', $response->getContent());
    }

    /**
     * @return void
     */
    public function testExportIsAnAttachment()
    {
      $response = $this->actingAs($this->user)->get('/accounts/acc-1/csv');

      $response->assertHeader(
        'Content-Disposition',
        'attachment; filename="upbank-spending-transactions.csv"'
      );
    }
}

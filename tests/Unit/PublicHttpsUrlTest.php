<?php

namespace Tests\Unit;

use App\Rules\PublicHttpsUrl;
use Tests\TestCase;

/**
 * Every case here uses an IP literal, so the rule never performs a DNS
 * lookup and the test does not depend on the network.
 */
class PublicHttpsUrlTest extends TestCase
{
    private $rule;

    protected function setUp(): void {
      parent::setUp();
      $this->rule = new PublicHttpsUrl();
    }

    /**
     * @return void
     */
    public function testAcceptsPublicHttpsUrl()
    {
      $this->assertTrue($this->rule->passes('action_url', 'https://203.0.113.10/hook'));
    }

    /**
     * @return void
     */
    public function testRejectsPlainHttp()
    {
      $this->assertFalse($this->rule->passes('action_url', 'http://203.0.113.10/hook'));
    }

    /**
     * The address an attacker actually wants on a cloud host.
     *
     * @return void
     */
    public function testRejectsLinkLocalMetadataAddress()
    {
      $this->assertFalse(
        $this->rule->passes('action_url', 'https://169.254.169.254/latest/meta-data/')
      );
    }

    /**
     * @dataProvider privateAddresses
     * @return void
     */
    public function testRejectsPrivateAddresses($url)
    {
      $this->assertFalse($this->rule->passes('action_url', $url));
    }

    public function privateAddresses()
    {
      return [
        'loopback'      => ['https://127.0.0.1/'],
        'class a'       => ['https://10.0.0.5/'],
        'class b'       => ['https://172.18.0.2/'],
        'class c'       => ['https://192.168.64.4/'],
        'ipv6 loopback' => ['https://[::1]/'],
        'ipv6 private'  => ['https://[fd00::1]/']
      ];
    }

    /**
     * @dataProvider malformedValues
     * @return void
     */
    public function testRejectsMalformedValues($value)
    {
      $this->assertFalse($this->rule->passes('action_url', $value));
    }

    public function malformedValues()
    {
      return [
        'no scheme' => ['203.0.113.10/hook'],
        'no host'   => ['https:///hook'],
        'not a url' => ['nonsense'],
        'empty'     => [''],
        'not text'  => [12345]
      ];
    }
}

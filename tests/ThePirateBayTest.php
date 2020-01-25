<?php

use PHPUnit\Framework\TestCase;

final class ThePirateBayTest extends TestCase
{
    public function testThePirateBay()
    {
        require_once __DIR__ . '/../thepiratebay.php';
        $torrents = searchTorrents('nginx');
        foreach ($torrents as $torrent) {
            foreach ($torrent as $value) {
                $this->assertNotEmpty($value);
            }
        }
    }
}

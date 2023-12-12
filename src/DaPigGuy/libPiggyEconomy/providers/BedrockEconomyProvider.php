<?php

declare(strict_types=1);

namespace DaPigGuy\libPiggyEconomy\providers;

use cooldogedev\BedrockEconomy\api\type\ClosureAPI;
use cooldogedev\BedrockEconomy\BedrockEconomy;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\currency\Currency;
use cooldogedev\BedrockEconomy\database\constant\Search;
use cooldogedev\BedrockEconomy\database\exception\RecordNotFoundException;
use cooldogedev\libSQL\exception\SQLException;
use pocketmine\player\Player;
use pocketmine\Server;

class BedrockEconomyProvider extends EconomyProvider
{
    private ClosureAPI $api;
    private Currency $currency;

    public static function checkDependencies(): bool
    {
        return Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy") !== null;
    }

    public function __construct()
    {
        $this->api = BedrockEconomyAPI::CLOSURE();
        $this->currency = BedrockEconomy::getInstance()->getCurrency();
    }

    public function getMonetaryUnit(): string
    {
        return $this->currency->symbol;
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $this->api->get(Search::EMPTY, $player->getName(), static fn(array $result) => $callback($result["amount"] ?? $this->currency->defaultAmount), static function(SQLException $exception): void {
            if ($exception instanceof RecordNotFoundException) {
                echo "Record not found";
                return;
            }
            echo $exception->getMessage();
        });
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->add(Search::EMPTY, $player->getName(), (int) $amount, 0, $callback ?? static fn() => null, static function(SQLException $exception): void {
            if ($exception instanceof RecordNotFoundException) {
                echo "Account not found";
                return;
            }
            echo $exception->getMessage();
        });
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->subtract(Search::EMPTY, $player->getName(), (int) $amount, 0, $callback ?? static fn() => null, static function(SQLException $exception): void {
            if ($exception instanceof RecordNotFoundException) {
                echo "Account not found";
                return;
            }
            echo $exception->getMessage();
        });
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->set(Search::EMPTY, $player->getName(), (int) $amount, 0, $callback ?? static fn() => null, static function(SQLException $exception): void {
            if ($exception instanceof RecordNotFoundException) {
                echo "Account not found";
                return;
            }
            echo $exception->getMessage();
        });
    }
}

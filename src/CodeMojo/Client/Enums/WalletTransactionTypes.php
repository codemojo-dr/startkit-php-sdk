<?php

namespace CodeMojo\Client\Enums;

/**
 * Class WalletTransactionTypes
 * @package CodeMojo\Client\Enums
 */
class WalletTransactionTypes {

    /**
     * Transactional type credits
     */
    const TRANSACTIONAL = 1;
    /**
     * Promotional type credits
     */
    const PROMOTIONAL = 2;
    /**
     * Gamification type credits
     */
    const GAMIFICATION = 3;

    /**
     * Redeem from any slot
     */
    const REDEEM_COMBINED = -1;
}
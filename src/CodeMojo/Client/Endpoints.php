<?php

namespace CodeMojo\Client;


/**
 * Class Endpoints
 * @package CodeMojo\Client
 */
class Endpoints
{
    const API_VERSION = 1.0;

    const SANDBOX = 1;
    const PRODUCTION = 2;
    const LOCAL = 3;

    const ENDPOINT_PRODUCTION = "https://api.codemojo.io";
    const ENDPOINT_SANDBOX = "https://sandbox.codemojo.io";
    const ENDPOINT_LOCAL = "http://lh-drewards-services:8888";

    const VERSION = "/v1";

    const ACCESS_TOKEN = "/oauth/access_token";

    /*
     * Wallet Endpoints
     */
    const BASE_WALLET = "/services/wallet";

    const WALLET_CREDITS = "/credits";
    const WALLET_CREDITS_BALANCE = "/credits/balance/%s";

    const WALLET_TRANSACTIONS_ALL = "/transactions/%d";
    const WALLET_TRANSACTIONS_USER = "/transactions/%s/%d";

    const WALLET_TRANSACTION = "/transaction/%s";
    const WALLET_TRANSACTION_UNFREEZE = "/transaction/release";

    /*
     * Loyalty Endpoints
     */
    const BASE_LOYALTY = "/services/loyalty";
    const LOYALTY_CALCULATE = "";
    const LOYALTY_SUMMARY = "/summary/%s";
    const REDEMPTION_CALCULATE = "/calculate-redemption";

    /*
     * Meta Endpoints
     */
    const BASE_META = "/services/meta";
    const META = "";
}
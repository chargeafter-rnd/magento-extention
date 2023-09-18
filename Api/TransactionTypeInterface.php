<?php

namespace Chargeafter\Payment\Api;

interface TransactionTypeInterface
{
    const TRANSACTION_TYPE_AUTHORIZATION = 'authorization';

    const TRANSACTION_TYPE_CAPTURE = 'capture';
}

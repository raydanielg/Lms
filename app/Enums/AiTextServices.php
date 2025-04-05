<?php

namespace App\Enums;

class AiTextServices
{

    // /v1/chat/completions ENDPOINT
    const GPT_4 = "gpt-4";
    const GPT_4_0613 = "gpt-4-0613";
    const GPT_4_32K = "gpt-4-32k";
    const GPT_4_32K_0613 = "gpt-4-32k-0613";
    const GPT_35_TURBO = "gpt-3.5-turbo";
    const GPT_35_TURBO_0613 = "gpt-3.5-turbo-0613";
    const GPT_35_TURBO_16K = "gpt-3.5-turbo-16k";
    const GPT_35_TURBO_16K_0613 = "gpt-3.5-turbo-16k-0613";

    // /v1/completions ENDPOINT
    const TEXT_DAVINCI_003 = "text-davinci-003";
    const TEXT_DAVINCI_002 = "text-davinci-002";
    const TEXT_DAVINCI_001 = "text-davinci-001";
    const TEXT_CURIE_001 = "text-curie-001";
    const TEXT_BABBAGE_001 = "text-babbage-001";
    const TEXT_ADA_001 = "text-ada-001";
    const DAVINCI = "davinci";
    const CURIE = "curie";
    const BABBAGE = "babbage";
    const ADA = "ada";

    const types = [
        self::ADA,
        self::BABBAGE,
        self::CURIE,
        self::DAVINCI,
        self::GPT_35_TURBO,
        self::GPT_35_TURBO_16K,
        self::GPT_4,
        self::GPT_4_32K,
    ];

    const chatCompletionsEndpoint = [
        self::GPT_4,
        self::GPT_4_0613,
        self::GPT_4_32K,
        self::GPT_4_32K_0613,
        self::GPT_35_TURBO,
        self::GPT_35_TURBO_0613,
        self::GPT_35_TURBO_16K,
        self::GPT_35_TURBO_16K_0613,
    ];

    const completionsEndpoint = [
        self::TEXT_DAVINCI_003,
        self::TEXT_DAVINCI_002,
        self::TEXT_DAVINCI_001,
        self::TEXT_CURIE_001,
        self::TEXT_BABBAGE_001,
        self::TEXT_ADA_001,
        self::DAVINCI,
        self::CURIE,
        self::BABBAGE,
        self::ADA,
    ];

}

<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Guest Pulse Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
class GuestPulseServer extends Server
{
    protected array $tools = [
        \App\Mcp\Tools\QuerySummaryTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}

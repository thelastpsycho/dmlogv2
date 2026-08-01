<?php

use App\Mcp\Servers\GuestPulseServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/guestpulse', GuestPulseServer::class)->middleware(['auth:sanctum']);

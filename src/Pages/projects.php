<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Projects', 'A showcase of my projects on leek.cafe!');
LayoutHelper::addStyleSheet('home.css');
?>

<div class="section">
    <div class="section-content">
        <h2>
          <a href="https://github.com/sorokya/eoweb">
            eoweb
            <img src="/img/projects/eoweb.webp" alt="eoweb screenshot"/>
          </a>
        </h2>
        <p class="subtitle">a browser-based client for the classic 2d mmorpg <a href="https://endless-online.com">Endless Online</a></p>
        <p>eoweb brings a legacy 2d mmorpg to the modern web by faithfully reimplementing the original 0.0.28 network protocol in typescript. it connects to classic eo servers via websocket, handling the full game lifecycle — from account creation and character selection to real-time multiplayer gameplay — all inside the browser with no plugins or downloads required.</p>
        <p>the technical centerpiece is a <strong>two-layer rendering architecture</strong>: a high-performance pixi.js engine renders the isometric game world with interpolated animation at 120ms ticks, while a lightweight preact ui overlays the hud, inventory, chat, and draggable dialog windows. game assets are dynamically assembled at runtime through a custom atlas system powered by web workers, and all game data (maps, sprites, dialogs) is cached in indexeddb for instant reloads. a modular controller architecture with 40+ domain-specific modules and a centralized event bus keeps the codebase maintainable despite the complexity of a full mmorpg client.</p>
        <p>built with <strong>vite, typescript, tailwind css, and daisyui</strong>, eoweb delivers a polished, themeable experience that rivals native clients — proving that even decades-old game protocols can live beautifully on the modern web.</p>
    </div>
</div>

<div class="section">
    <div class="section-content">
        <h2>
          <a href="https://reoserv.net">
            reoserv
            <img src="/img/projects/reoserv.webp" alt="reoserv screenshot"/>
          </a>
        </h2>
        <p class="subtitle">High-performance, Rust-powered server emulator for the classic 2d mmorpg <a href="https://endless-online.com">Endless Online</a></p>
        <p>
          Built with Rust's async runtime (Tokio),
          it delivers a modern, production-grade reimplementation of the original game server, supporting both
          traditional TCP sockets and WebSocket connections for browser-based clients. The emulator handles the
          full game loop including world simulation, character progression, combat formulas, NPC AI, quests,
          shops, spells, and guild systems.
        </p>
        <p>
          The architecture leverages Rust's memory safety and zero-cost abstractions to achieve high throughput
          with minimal resource usage. Key technical achievements include: an actor-based world simulation with
          configurable tick rates; automatic database migrations supporting both MySQL/MariaDB and SQLite;
          hot-reloadable configuration via file watchers (settings, commands, formulas, arenas, packet rate
          limits, and localization); connection rate limiting and per-IP throttling; and Argon2 password hashing;
        </p>
    </div>
</div>

<?php LayoutHelper::end(); ?>

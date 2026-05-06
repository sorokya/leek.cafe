<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Developing EOWeb', 'Page describing the development story of EOWeb');
LayoutHelper::addStyleSheet('home.css');
?>

<div class="section">
  <div class="section-content">

  <h1>Developing EOWeb</h1>
  <small>May 6th 2026</small>
<br />
<br />

  <h2>Prior attempts</h2>
  <p>
    EOWeb is not the first web based client for Endless Online. The earliest <em>good</em> attempt I can remember was
    <a href="https://github.com/atbabers/Aphelion">Aphelion</a>. I know I played it a bit when it launched but I don't
    remember much. It mostly focused on Arena games. The source is available there on GitHub although who knows if anyone
    has tried to get it running since 2018.
  </p>

  <p>
    I'm pretty sure <a href="https://exile-studios.com/">@exile</a>, and <a href="https://apollo-games.com/">@apollo</a> both
    had different Java based clients in progress that could run in the browser via <a href="https://en.wikipedia.org/wiki/Java_applet">Java Applets</a>.
  </p>

  <p>
    There have also been attempts to port pc based clients to the web via Web Assembly.
  </p>

  <h2>Early days</h2>

  <p>
    I'm just going to list a bunch of dates and screenshots here to show progress
  </p>

  <img src="/img/posts/2026-05-06-reoserv-ws.webp" alt="REOSERV accepting web socket connections"/>
  <p class="subtitle">April 29th 2025 - WebSocket support added to REOSERV. Left: Web page connecting to reoserv; Right: Vanilla client showing
web socket player.</p>

<img src="/img/posts/2026-05-06-eoweb-v0.webp" alt="First version of eoweb"/>
  <p class="subtitle">May 2nd 2025 - First public screenshot of EOWeb. At this point there was no net code. Was just a map renderer with
  naked character rendering and glitchy walk animation</p>

  </div>
</div>

<?php LayoutHelper::end();

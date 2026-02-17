<?php

declare(strict_types=1);

use App\Utils\LayoutHelper;

LayoutHelper::assertRequestMethod('GET');
LayoutHelper::begin('Home', 'The homepage for leek.cafe! My personal site of projects, thoughts, and more!');
LayoutHelper::addStyleSheet('home.css');
?>

<div class="section">
    <div class="section-content">
        <h2>Welcome to leek.cafe!</h2>
        <p>
            This is my personal website where I share
            <a href="/projects">things I'm working on</a>, thoughts about my favorite
            <a href="/games">video games</a>, <a href="/anime">anime</a>, <a href="/movies">movies</a>
            and more! Feel free to explore and check out my <a href="/blog">blog</a> where I post updates about my projects and other random thoughts.
        </p>
    </div>
</div>

<div class="section">
    <div class="section-content">
        <h2>About Me</h2>
        <ul>
            <li>Name: Richard Leek (<a href="https://github.com/sorokya" target="_blank">@sorokya</a>)</li>
            <li>Occupation: Software Developer</li>
            <li>Age: Born in 1995; you do the math</li>
            <li>Location: 50% USA; 50% Sweden</li>
            <li>Relationship Status: <em>Happily Married</em></li>
            <li>Hobbies: Programming, gaming, tv, films, anime</li>
        </ul>
    </div>
</div>

<div class="section">
    <div class="section-content">
        <h2>Funny Badges</h2>
        <div class="badges">
            <img src="/img/88x31/drpepper.gif" alt="DrPepper" title="I like Dr Pepper" />
            <img src="/img/88x31/notepad.gif" alt="Notepad" title="Powered by Notepad" />
            <a href="https://www.php.net/" target="_blank"><img src="/img/88x31/php.gif" alt="PHP" title="Built with PHP" /></a>
            <img src="/img/88x31/pogo.gif" alt="Pogo" title="Does anyone remember pogo.com?" />
            <img src="/img/88x31/imagination.gif" alt="Imagination" title="Made with pure imagination" />
            <img src="/img/88x31/playstation.gif" alt="PlayStation" title="I love PlayStation" />
            <img src="/img/88x31/winrar.gif" alt="WinRAR" title="I have a WinRAR license" />
            <img src="/img/88x31/repair.gif" alt="Right to Repair" title="I support the Right to Repair" />
            <img src="/img/88x31/runbox.gif" alt="Runbox" title="I use Runbox for email" />
            <img src="/img/88x31/backtolan.gif" alt="Screw ya'll I'm going back to my LAN" title="There's no place like LAN" />
            <img src="/img/88x31/theoldnet.gif" alt="The Old Net" title="Browse the old net with me at theoldnet.com" />
            <img src="/img/88x31/fckgoogle.gif" alt="Fuck Google" title="What happened to don't be evil?" />
            <img src="/img/88x31/winamp.gif" alt="Winamp" title="It really whips the llama's ass" />
            <a href="https://xkcd.com" target="_blank"><img src="/img/88x31/xkcd.gif" alt="xkcd" title="xkcd is the best webcomic" /></a>
            <img src="/img/88x31/anythingbutchrome.gif" alt="Anything but Chrome" title="Anything but Chrome" />
            <img src="/img/88x31/bestviewedondesktop.gif" alt="Best Viewed on Desktop" title="Sorry mobile users, this site is best viewed on desktop" />
            <img src="/img/88x31/animeeyes.gif" alt="Anime Eyes" title="I have anime eyes" />
            <img src="/img/88x31/imissxp.gif" alt="I miss xp" title="The best windows" />
            <img src="/img/88x31/bookmark.gif" alt="Bookmark" title="Don't forget to bookmark this site!" />
            <img src="/img/88x31/cssisdifficult.gif" alt="CSS is Difficult" title="I try my best" />
            <img src="/img/88x31/darkmode.gif" alt="Made for dark mode" title="You're welcome" />
            <a href="https://www.debian.org" target="_blank"><img src="/img/88x31/debian.gif" alt="Debian" title="Powered by Debian" /></a>
            <img src="/img/88x31/dreamcast.gif" alt="Dreamcast" title="Goodnight sweet prince" />
            <a href="https://www.firefox.com" target="_blank"><img src="/img/88x31/firefox.gif" alt="Firefox" title="Firefox is the superior browser" /></a>
            <a href="https://scp-wiki.wikidot.com/" target="_blank"><img src="/img/88x31/scp.gif" alt="SCP Foundation" title="The SCP Foundation is real and I'm a part of it" /></a>
            <img src="/img/88x31/88x31.gif" alt="88x31" title="Haha so meta" />
            <img src="/img/88x31/gplv3.gif" alt="GPLv3" title="Muh freedoms" />
            <img src="/img/88x31/fckfb.gif" alt="Fuck Facebook" title="I have one because my wife makes me" />
            <img src="/img/88x31/gnu-linux.gif" alt="GNU/Linux" title="I'd like to interject for a moment..." />
            <a href="https://archive.org" target="_blank"><img src="/img/88x31/internetarchive.gif" alt="Internet Archive" title="If we lose them we're fucked" /></a>
            <img src="/img/88x31/privacy.gif" alt="Internet privacy" title="What's that?" />
            <img src="/img/88x31/webtv.gif" alt="WebTV" title="Before my time..." />
            <img src="/img/88x31/learn-html.gif" alt="Learn HTML" title="or else!" />
        </div>
    </div>
</div>



<?php LayoutHelper::end();

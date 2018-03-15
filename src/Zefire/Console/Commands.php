<?php

\Command::name('clear-compiled', 'Zefire\Console\Controller@clearViews');
\Command::name('clear-sessions', 'Zefire\Console\Controller@clearSessions');
\Command::name('clear-logs', 'Zefire\Console\Controller@clearLogs');
\Command::name('work', 'Zefire\Console\Controller@work');
\Command::name('clear-queue', 'Zefire\Console\Controller@clearQueue');
\Command::name('up', 'Zefire\Console\Controller@up');
\Command::name('down', 'Zefire\Console\Controller@down');
\Command::name('controller', 'Zefire\Console\Controller@generateController');
\Command::name('middleware', 'Zefire\Console\Controller@generateMiddleware');
\Command::name('job', 'Zefire\Console\Controller@generateJob');
\Command::name('event', 'Zefire\Console\Controller@generateEvent');
\Command::name('model', 'Zefire\Console\Controller@generateModel');
\Command::name('auth', 'Zefire\Console\Controller@generateAuth');
\Command::name('command', 'Zefire\Console\Controller@generateCommand');
\Command::name('list-routes', 'Zefire\Console\Controller@listRoutes');
\Command::name('key', 'Zefire\Console\Controller@key');
\Command::name('token', 'Zefire\Console\Controller@key');
<?php
require_once "Gerrit.php";
require_once "File.php";
require_once "Telegram.php";
require_once "MessageBuilder.php";

function main(Array $config): void {
    $gerrit = new Gerrit(
        ["n" => 50, "q" => "status:merged -age:2h"],
        $config['gerrit']
    );

    if(empty($gerrit->changes)){
        echo "No Changes detected! \nexiting...\n";
        return;
    }
    
    $cache = new File($config['cache']);

    $telegram = new Telegram($config['telegram']);

    foreach($gerrit->joinChangesPerRepository($cache) as $commits){
        $telegram->sendMessage(MessageBuilder::build($commits, $config['gerrit']));
    }
}

function runAsLoop(Array $config): void {
    while (true) {
        main($config);
        echo "Sleeping for 15 Minutes\n";
        sleep(15*60);
    }

}

function run(Array $config): void {
    main($config);
}
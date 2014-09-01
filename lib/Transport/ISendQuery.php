<?php

namespace Transport;
use Buzz\Browser;
use Buzz\Message\Response;
use Transport\Providers\Provider;


interface ISendQuery {
    /**
     * Default implementation
     * @param \Buzz\Browser $browser
     * @return Response
     */
    public function __sendQuery(Browser $browser);

    public function addProvider(Provider $provider);
}
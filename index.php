<?php

if (!session::checkAccessControl('blocks_allow')){
    return;
}

moduleloader::includeModule ('blocks');
<?php

return [
    // array of listeners that will be executed once the execution is done
    'listeners' => [],

    // if you want to stock the user who started the export
    'with_owner' => false,

    // the guard
    'guard' => 'web',

    // the foreign key name in the exports table
    'owner_id' => 'user_id',

    // The Authenticable class
    'owner_class' => null,

    // Number of records in each sub export
    'records_per_file' => 500,

];

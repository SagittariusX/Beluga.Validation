<?php


return [

   // Main errors of the abstract Validator class
   0   => 'NO REQUEST: The required %s value "%s" from %s source is undefined.',
   1   => 'The %s field can not use a empty value!',
   2   => 'NO REQUEST: The required %s value "%s" from %s source do no match the required value.',

   // DateValidator + DateTimeValidator + …more
   3   => 'Not a valid %s value!',
   4   => 'The value is lower than allowed! (Allowed minimal value is %s)',
   5   => 'The value is bigger than allowed! (Allowed maximal value is %s)',
   6   => 'This %s value is not a valid string!',
   7   => 'The %s value is shorter than allowed! (Allowed minimal length is %d)',
   8   => 'The %s value is longer than allowed! (Allowed maximal length is %d)',
   9   => 'The %s value uses an invalid format!',
   10  => 'The %s value is wrong/invalid!',

   // DateValidator
   11  => 'Date',

   // DateTimeValidator
   12  => 'Date+Time',

   // MailAddressValidator
   13  => 'Mail address',
   14  => 'Mail address without a valid domain part!',
   15  => 'Locally reserved mail address domain-part!',
   16  => 'Reserved mail address domain-part!',
   17  => 'Mail address, pointing to geographic TLDs!',
   18  => 'Localized unicode mail address domain parts!',
   19  => 'Domain part without TLD definition!',
   20  => 'Domain part with unknown TLD!',
   21  => 'Domain part is a known dynamic DNS service!',
   22  => 'Mail address with forbidden TLD part!',
   23  => 'Mail address with forbidden domain part!',
   24  => 'Mail address with invalid domain-part! (No MX host)',

   // UrlValidator
   25 => 'Web address',
   26 => 'Not a valid web address! Missing a scheme/protocol.',
   27 => 'Not a valid web address!',
   28 => 'Invalid web address format! Scheme "%s" is not supported.',
   29 => 'Invalid URL! Insecure auth/login information.',
   30 => 'Web addresses with IP address are not allowed.',
   31 => 'Web address uses a non standard port!',
   32 => 'Web address uses a not accepted url shortener service!',
   33 => 'Invalid web address without a valid TLD!',
   34 => 'Web address without a known TLD!',
   35 => 'Web address uses a reserved host (part)!',
   36 => 'Web address uses a local host (part)!',
   37 => 'Web address points to a dynamic DNS service!',
   38 => 'Web address contains some query parameters!',
   39 => 'Web address contains more than %d query parameters!',
   40 => 'Web address contains to much query data!',
   41 => 'Web address security issue! (open redirection bug usage)',
   42 => 'Illegal web address (forbidden TLD part)!',
   43 => 'Illegal web address (forbidden domain part)!',

   // TextValidator
   44 => 'The %s value contains line breaks.',
   45 => 'The %s value contains %d line breaks. (Allowed are max. %d!)',
   46 => 'The %s value contains HTML markup.',
   47 => 'The %s value contains some URLs.',
   48 => 'The %s value contains more than %d URLs (web addresses)!',
   49 => 'The %s value contains some mail addresses.',
   50 => 'The %s value contains spam content.',
   51 => 'The %s value contains spam content. (entities)',
   52 => 'The %s value contains content that looks like spam.',
   53 => 'The %s value contains encoded content',

   // Integer validator
   54 => 'Integer',

   // ZipCodeValidator
   55 => 'The %s value defines an unknown postal code for country %s!'
   
];


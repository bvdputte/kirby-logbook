<?php

namespace bvdputte\kirbyLogbook;

use Kirby\Exception\LogicException;
use Kirby\Exception\PermissionException;
use Dir;

class Logbook
{
    // Getters

    public static function getLogfiles()
    {
        $logfiles = Dir::files(kirby()->root('logs'));

        return array_values(array_diff($logfiles, option('bvdputte.logbook.hide')));
    }

    public static function getDefaultLogfile()
    {
        $logfiles = self::getLogfiles();

        if (
            (option('bvdputte.logbook.default') != '') &&
            (in_array(option('bvdputte.logbook.default'), $logfiles))
        ) {
            return option('bvdputte.logbook.default');
        }

        if (!empty($logfiles)) return (string)$logfiles[0];

        return '';

    }

    public static function getMaxLogLines()
    {
        return option('bvdputte.logbook.maxLogLines');
    }

    public static function getPaginationSize()
    {
        return option('bvdputte.logbook.paginationSize');
    }

    public static function hasKirbyLogPlugin()
    {
        return is_a(site()->logger(), 'bvdputte\kirbyLog\Logger');
    }


    // Fetch latest log entries

    public static function tail($logname = null, $filter = null, $lines = null, $adaptive = true)
    {
        if (count(self::getLogfiles()) == 0) return false;

        $logname = is_null($logname) ? self::getDefaultLogfile() : $logname;
        if (is_null($lines)) $lines = self::getmaxLogLines();

        // https://stackoverflow.com/questions/15025875/what-is-the-best-way-to-read-last-lines-i-e-tail-from-a-file-using-php
        // Open file
        $f = fopen(kirby()->root('logs') . '/' . $logname, 'rb');
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        fseek($f, -1, SEEK_END);
        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // Close file
        fclose($f);

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        $logLines = array_filter(array_reverse(explode(PHP_EOL,$output)));

        if ($filter) {
            $logLines = array_filter(
                $logLines,
                function ($line) use ($filter) {
                    return !strpos($line, '['.$filter.']') === false;
                }
            );
        }

        // Format Kirbylog plugin loglines as columns
        if(self::hasKirbyLogPlugin()) {
            $formattedLogLines = [];
            foreach($logLines as $logLine) {
                $formattedLine = explode('] ', $logLine);

                // Unexpected format (['timestamp','type','content']), return as lines
                if (count($formattedLine) != 3) return $logLines;

                // Else format each line into 3 columns
                $formattedObj['timestamp'] = str_replace('[','',$formattedLine[0]);
                $formattedObj['type'] = str_replace('[','',$formattedLine[1]);
                $formattedObj['content'] = $formattedLine[2];
                $formattedLogLines[] = $formattedObj;
            }

            return $formattedLogLines;
        }

        return $logLines;
    }

    // Permissions helper

    public static function hasAccess($user)
    {
        if ($user === null) {
            return false;
        }

        return $user->role()->id() == 'admin';
    }


    public static function checkAccess($user)
    {
        if (!self::hasAccess($user)) {
            throw new PermissionException([
                'key'  => 'logbook.permission',
                'data' => ['Only admin roles have access']
            ]);
        }

        return true;
    }
}

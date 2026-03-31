<?php

namespace App\Actions\Sequences;

use Throwable;
use App\Models\Sequence;
use Illuminate\Support\Facades\Log;
use App\Exceptions\SequenceNotFoundException;

class GetSequenceAction
{
    public function handle(string $key): string
    {
        try {
            $dateToday = date('Y-m-d');
            $sequence = Sequence::where('key', $key)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                throw new SequenceNotFoundException($key);
            }

            if ($sequence->date != $dateToday) {
                $sequence->value = 0;
                $sequence->date = $dateToday;
            }

            $sequence->value++;
            $sequence->save();

            return sprintf(
                $sequence->formatter,
                str_replace('-', '', $sequence->date),
                $sequence->value
            );
        } catch (Throwable $th) {
            Log::debug(__METHOD__, [
                'key' => $key,
            ]);

            throw $th;
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            ['name' => 'Regular', 'price' => 3000],
        ];

        foreach ($tickets as $ticket) {
            \App\Models\Ticket::create($ticket);
        }
    }
}

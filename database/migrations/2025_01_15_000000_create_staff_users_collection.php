<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MongoDB, we don't need to create the collection explicitly
        // It will be created automatically when the first document is inserted
        // But we can create indexes if needed
        
        try {
            // Create indexes for better performance
            $collection = \DB::connection('mongodb')->getCollection('staff_users');
            
            // Create indexes with error handling
            try {
                $collection->createIndex(['email' => 1], ['unique' => true]);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
            
            try {
                $collection->createIndex(['invitation_token' => 1]);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
            
            try {
                $collection->createIndex(['status' => 1, 'role' => 1]);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
            
            try {
                $collection->createIndex(['invitation_sent_at' => 1]);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
        } catch (\Exception $e) {
            // Collection might already exist, that's okay
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the collection
        \DB::connection('mongodb')->collection('staff_users')->drop();
    }
}; 
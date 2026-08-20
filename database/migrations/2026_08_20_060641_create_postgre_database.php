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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username', 64);
            $table->text('user_photo')->nullable();
            $table->text('bio')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->string('email')->unique();
            $table->text('password');
            $table->enum('role', ['User', 'Admin'])->default('User');
            $table->boolean('is_suspended')->default(false);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        /*
        // This is no longer needed, since its impractical and dangerous. We use Cache instead to handle this
        Schema::create('authentications', function (Blueprint $table) {
            $table->increments('authNo');
            $table->string('email', 50);
            $table->integer('authCode')->nullable();
        });
        */

        Schema::create('posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->string('title');
            $table->text('description');
            $table->text('post_photo')->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->timestamps();
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('post_id')->constrained('posts', 'post_id');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->text('description');
            $table->text('comment_photo')->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('post_id')->constrained('posts', 'post_id');
            $table->timestamps();
        });

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->string('title');
            $table->text('description');
            $table->text('feedback_photo')->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('report_desc');
            $table->string('report_type');
            $table->integer('report_no');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->enum('status', ["Pending", "Resolved"]);
            $table->timestamps();
            $table->softDeletes();
            // reportNo is polymorphic (references posts or comments based on reportType), so no FK constraint
        });

        Schema::create('upvotes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('content_type', 8);
            $table->integer('content_no');
            $table->timestamps();
            // contentNo is polymorphic (references posts or comments based on contentType), so no FK constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order to respect foreign key constraints
        Schema::dropIfExists('upvotes');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
    }
};

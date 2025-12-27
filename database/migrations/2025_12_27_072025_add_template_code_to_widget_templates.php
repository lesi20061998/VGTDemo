  <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('widget_templates', function (Blueprint $table) {
            $table->longText('template_code')->nullable()->after('default_settings');
            $table->longText('template_css')->nullable()->after('template_code');
            $table->longText('template_js')->nullable()->after('template_css');
        });
    }

    public function down(): void
    {
        Schema::table('widget_templates', function (Blueprint $table) {
            $table->dropColumn(['template_code', 'template_css', 'template_js']);
        });
    }
};

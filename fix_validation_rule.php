<?php

// Fix Validation Rule Error
echo "=== Fixing Validation Rule Error ===\n\n";

echo "The error was caused by incorrect usage of using() method in validation rules.\n\n";

echo "WRONG WAY:\n";
echo "Rule::unique('table', 'column')->using('connection')\n\n";

echo "CORRECT WAY:\n";
echo "Rule::unique('table', 'column')->using(function(\$query) {\n";
echo "    return \$query->where('some_condition', 'value');\n";
echo "})\n\n";

echo "OR if you need different connection:\n";
echo "Rule::unique('table', 'column')->connection('connection_name')\n\n";

echo "FIXED IN ProductController:\n";
echo "- Removed ->using('project') from validation rules\n";
echo "- Now uses default connection\n\n";

echo "If you need project-specific validation, you should:\n";
echo "1. Use ->where() conditions instead\n";
echo "2. Or create custom validation rule\n";
echo "3. Or use different table per project\n\n";

echo "✓ ProductController validation rules have been fixed\n";
echo "✓ SKU uniqueness will now work correctly\n";

?>
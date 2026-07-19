import os
import re

directory = '/Users/sengkea/Documents/GitHub/Individual/BackEnd/app/Models'

for filename in os.listdir(directory):
    if not filename.endswith('.php'):
        continue

    filepath = os.path.join(directory, filename)
    with open(filepath, 'r') as f:
        content = f.read()

    if 'HasQueryScopes' in content:
        continue

    # 1. Add use App\Traits\HasQueryScopes; after namespace
    content = re.sub(r'(namespace\s+App\\Models;)', r'\1\n\nuse App\\Traits\\HasQueryScopes;', content, count=1)

    has_code = "'code'" in content or '"code"' in content
    searchable_str = "\n    protected array $searchable = ['code'];\n" if has_code else "\n    protected array $searchable = [];\n"

    # 2. Add use HasQueryScopes; and $searchable inside the class body
    class_match = re.search(r'class\s+[a-zA-Z0-9_]+[^{]*{', content)
    
    if class_match:
        brace_idx = class_match.end()
        # insert right after brace
        insert_str = "\n    use HasQueryScopes;\n" + searchable_str
        content = content[:brace_idx] + insert_str + content[brace_idx:]
    
    with open(filepath, 'w') as f:
        f.write(content)

print("Models updated.")

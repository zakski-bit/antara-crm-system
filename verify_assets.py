import os
import re

admin_dir = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM\crm-portfolio-live\admin"
missing = []

for f in os.listdir(admin_dir):
    if f.endswith('.html'):
        p = os.path.join(admin_dir, f)
        with open(p, 'r', encoding='utf-8') as fp:
            content = fp.read()
        
        matches = re.findall(r'(?:href|src)=["\']([^"\']+)["\']', content)
        for h in matches:
            if h.startswith('http') or h.startswith('#') or h.startswith('javascript:'):
                continue
            # remove query string if any
            clean_h = h.split('?')[0]
            target = os.path.normpath(os.path.join(admin_dir, clean_h))
            if not os.path.exists(target):
                missing.append((f, h, target))

if not missing:
    print("SUCCESS: ALL ASSETS AND INTERNAL LINKS EXIST PERFECTLY!")
else:
    print(f"FOUND {len(missing)} MISSING:")
    for f, h, t in missing:
        print(f"File {f} -> {h} (Expected: {t})")

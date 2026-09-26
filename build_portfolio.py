import os
import shutil
import re

BASE_DIR = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM"
FRONTEND_DIR = os.path.join(BASE_DIR, "FrontEnd-CRM", "autoexpert-php")
BACKEND_TEMPLATE_DIR = os.path.join(BASE_DIR, "BackEnd-CRM", "docker-php", "php", "template")
OUTPUT_DIR = os.path.join(BASE_DIR, "crm-portfolio-live")

print("Building Static Portfolio Showcase in:", OUTPUT_DIR)
os.makedirs(OUTPUT_DIR, exist_ok=True)

# 1. Copy Frontend Assets
frontend_asset_dirs = ["css", "fonts", "images", "js", "videos"]
for folder in frontend_asset_dirs:
    src = os.path.join(FRONTEND_DIR, folder)
    dst = os.path.join(OUTPUT_DIR, folder)
    if os.path.exists(src):
        print(f"Copying {folder}...")
        if os.path.exists(dst):
            shutil.rmtree(dst)
        shutil.copytree(src, dst)

for single_file in ["favicon.ico", "favicon.png"]:
    src = os.path.join(FRONTEND_DIR, single_file)
    dst = os.path.join(OUTPUT_DIR, single_file)
    if os.path.exists(src):
        shutil.copy2(src, dst)

# 2. Copy Backend Assets to admin/assets
admin_dir = os.path.join(OUTPUT_DIR, "admin")
os.makedirs(admin_dir, exist_ok=True)
admin_assets_src = os.path.join(BACKEND_TEMPLATE_DIR, "assets")
admin_assets_dst = os.path.join(admin_dir, "assets")

backend_asset_subdirs = ["css", "fonts", "img", "js", "plugins"]
os.makedirs(admin_assets_dst, exist_ok=True)
for sub in backend_asset_subdirs:
    src = os.path.join(admin_assets_src, sub)
    dst = os.path.join(admin_assets_dst, sub)
    if os.path.exists(src):
        print(f"Copying backend assets/{sub}...")
        if os.path.exists(dst):
            shutil.rmtree(dst)
        shutil.copytree(src, dst)

print("Assets successfully copied!")

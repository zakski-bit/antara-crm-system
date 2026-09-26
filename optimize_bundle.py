import os
import shutil
import glob
import subprocess

root = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM\crm-portfolio-live"

# 1. Optimize videos: 15-second loop is perfect for hero video backgrounds
for v in ["hitam_1min.mp4", "warni_1min.mp4"]:
    vpath = os.path.join(root, "videos", v)
    outpath = os.path.join(root, "videos", "opt_" + v)
    if os.path.exists(vpath):
        cmd = [
            "ffmpeg", "-ss", "00:00:00", "-i", vpath, "-t", "15",
            "-vf", "scale=960:-2", "-c:v", "libx264", "-crf", "30",
            "-an", "-preset", "faster", "-y", outpath
        ]
        subprocess.run(cmd, check=True)
        os.replace(outpath, vpath)
        print("Optimized video:", v, os.path.getsize(vpath) / 1024, "KB")

# 2. Clean unused images in admin/assets/img
admin_img = os.path.join(root, "admin", "assets", "img")
keep_imgs = {
    "logo-antaradark.png", "logo-antara.png", "logo-antara-light.png",
    "avatar-01.jpg", "avatar-02.jpg", "avatar-03.jpg"
}

for root_dir, dirs, files in os.walk(admin_img, topdown=False):
    for f in files:
        if f not in keep_imgs:
            try:
                os.remove(os.path.join(root_dir, f))
            except:
                pass
    for d in dirs:
        dp = os.path.join(root_dir, d)
        if not os.listdir(dp):
            try:
                os.rmdir(dp)
            except:
                pass

print("Cleaned admin/assets/img!")

# 3. Clean unused fonts in admin/assets/fonts
admin_fonts = os.path.join(root, "admin", "assets", "fonts")
if os.path.exists(admin_fonts):
    for f in os.listdir(admin_fonts):
        if f.endswith(".svg") or f.endswith(".ttf") or f.endswith(".eot"):
            try:
                os.remove(os.path.join(admin_fonts, f))
            except:
                pass

print("Cleaned admin/assets/fonts!")

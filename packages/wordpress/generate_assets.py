from PIL import Image
import os

assets_dir = r"c:\Users\rayel\OneDrive\Documents\Business\RayelsConsulting\Business code projet 1\rayels-loi25\packages\wordpress\assets"
banner_src = os.path.join(assets_dir, "banner.png")
icon_src = os.path.join(assets_dir, "icon1.png")

# Variations to generate
variations = [
    # FileName, Source, Size (W, H)
    ("icon-256x256.png", icon_src, (256, 256)),
    ("icon-128x128.png", icon_src, (128, 128)),
    ("banner-1544x500.png", banner_src, (1544, 500)),
    ("banner-772x250.png", banner_src, (772, 250)),
]

def generate_variations():
    print("Generating WordPress plugin assets...")
    for filename, src_path, size in variations:
        try:
            with Image.open(src_path) as img:
                # Use Resampling.LANCZOS for high quality (formerly ANTIALIAS)
                resample = getattr(Image, 'Resampling', Image).LANCZOS
                resized = img.resize(size, resample=resample)
                output_path = os.path.join(assets_dir, filename)
                resized.save(output_path, "PNG")
                print(f"Created: {filename} ({size[0]}x{size[1]})")
        except Exception as e:
            print(f"Error creating {filename}: {e}")

if __name__ == "__main__":
    generate_variations()

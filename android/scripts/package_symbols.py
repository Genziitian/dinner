import os
import zipfile
import shutil

def package_symbols(aab_path=None, zip_output_path=None):
    jni_dirs = [
        "/Users/tusharrrsingh/.gradle/caches/9.4.1/transforms/9ea11e8cb18ba5ad4c0d17df35b26d57/transformed/graphics-path-1.0.1/jni",
        "/Users/tusharrrsingh/.gradle/caches/8.10.2/transforms/b35e4f04731a9da827530ef7ea854001/transformed/graphics-path-1.0.1/jni"
    ]
    
    valid_jni_dir = None
    for d in jni_dirs:
        if os.path.exists(d):
            valid_jni_dir = d
            break
            
    if not valid_jni_dir:
        print("ERROR: graphics-path jni directory not found!")
        return

    print(f"Using graphics-path JNI directory: {valid_jni_dir}")

    # 1. Create standalone native-debug-symbols.zip for Play Console upload
    if zip_output_path:
        with zipfile.ZipFile(zip_output_path, 'w', zipfile.ZIP_DEFLATED) as z:
            for abi in sorted(os.listdir(valid_jni_dir)):
                abi_path = os.path.join(valid_jni_dir, abi)
                if os.path.isdir(abi_path):
                    for f in os.listdir(abi_path):
                        if f.endswith('.so'):
                            full_path = os.path.join(abi_path, f)
                            arcname = f"{abi}/{f}"
                            z.write(full_path, arcname)
                            print(f"Wrote to zip: {arcname}")
        print(f"Successfully generated standalone symbols: {zip_output_path}")

    # 2. Inject into AAB BUNDLE-METADATA if AAB exists
    if aab_path and os.path.exists(aab_path):
        # We need to add files under BUNDLE-METADATA/com.android.tools.build.debugsymbols/<abi>/<so>
        temp_aab = aab_path + ".temp"
        with zipfile.ZipFile(aab_path, 'r') as zin, zipfile.ZipFile(temp_aab, 'w', zipfile.ZIP_DEFLATED) as zout:
            for item in zin.infolist():
                # Skip any existing debugsymbols we might be replacing
                if not item.filename.startswith("BUNDLE-METADATA/com.android.tools.build.debugsymbols/"):
                    zout.writestr(item, zin.read(item.filename))
            
            # Inject debug symbols
            for abi in sorted(os.listdir(valid_jni_dir)):
                abi_path = os.path.join(valid_jni_dir, abi)
                if os.path.isdir(abi_path):
                    for f in os.listdir(abi_path):
                        if f.endswith('.so'):
                            full_path = os.path.join(abi_path, f)
                            arcname = f"BUNDLE-METADATA/com.android.tools.build.debugsymbols/{abi}/{f}"
                            zout.write(full_path, arcname)
                            print(f"Injected into AAB: {arcname}")
        
        shutil.move(temp_aab, aab_path)
        print(f"Successfully embedded native debug symbols into AAB: {aab_path}")

if __name__ == "__main__":
    import sys
    project_root = "/Users/tusharrrsingh/Desktop/dinner"
    aab = os.path.join(project_root, "android/app/build/outputs/bundle/release/app-release.aab")
    zip_out = os.path.join(project_root, "native-debug-symbols.zip")
    package_symbols(aab_path=aab if os.path.exists(aab) else None, zip_output_path=zip_out)

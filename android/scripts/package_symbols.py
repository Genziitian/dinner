import os
import zipfile

def package_symbols(zip_output_path):
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

    # Create standalone native-debug-symbols.zip for Play Console upload
    # Under App bundle explorer -> Downloads -> Assets -> Native debug symbols
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

if __name__ == "__main__":
    project_root = "/Users/tusharrrsingh/Desktop/dinner"
    zip_out = os.path.join(project_root, "native-debug-symbols.zip")
    package_symbols(zip_output_path=zip_out)


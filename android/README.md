# DinePOS Android Application

Production-grade, Play Store-ready native Android client for **DinePOS (Restaurant Billing & Order Management System)**.

---

## 🛠️ Architecture & Tech Stack

- **UI Framework**: Jetpack Compose (Declarative UI) with Material Design 3
- **Language**: Kotlin 2.0+
- **Architecture**: Clean Architecture (Presentation, Domain, Data, Core)
- **Networking**: Retrofit 2, OkHttp 4, Kotlinx Serialization
- **Security**: Android Keystore `EncryptedSharedPreferences`, Token-based Authentication
- **Receipts**: Native QR Code generation using ZXing
- **Target SDK**: Android 15 (API 35), Min SDK: Android 7.0 (API 24)

---

## 🚀 Opening in Android Studio

1. Launch **Android Studio**.
2. Click **Open** and select the `/dinner/android` folder.
3. Android Studio will automatically sync Gradle and load the project.
4. Select an Android Emulator or connected physical device and press **Run** (`Shift + F10`).

---

## 📦 Building Play Store Release Artifacts

### 1. Build Android App Bundle (.aab) for Google Play:
```bash
./gradlew bundleRelease
```
The generated bundle will be located at:
`app/build/outputs/bundle/release/app-release.aab`

### 2. Build Signed Release APK:
```bash
./gradlew assembleRelease
```
The generated APK will be located at:
`app/build/outputs/apk/release/app-release.apk`

---

## 🌐 Connecting to Backend

By default, the app is pre-configured to connect to `http://10.0.2.2:8000/` (the host development machine from an Android emulator) or you can tap the **Settings icon (⚙️)** on the login screen to enter your local IP (e.g., `http://192.168.1.100:8000/`) or production domain (`https://pos.yourrestaurant.com/`).

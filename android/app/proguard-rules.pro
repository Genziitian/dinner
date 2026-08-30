# ProGuard / R8 Rules for DinePOS Production Release

-dontwarn **
-ignorewarnings

# Keep Kotlin / Coroutines
-keepattributes *Annotation*, InnerClasses, Signature, EnclosingMethod
-dontnote kotlinx.serialization.SerializationKt
-keepclassmembers class * {
    *** Companion;
}
-keepclasseswithmembers class * {
    kotlinx.serialization.KSerializer serializer(...);
}
-keep class kotlinx.serialization.** { *; }
-keep,allowobfuscation,allowshrinking class * {
    @kotlinx.serialization.Serializable class *;
}

# Retrofit 2 & OkHttp 3/4
-keep class retrofit2.** { *; }
-keep class okhttp3.** { *; }
-keep class okio.** { *; }
-keepclasseswithmembers class * {
    @retrofit2.http.* <methods>;
}

# Data & Domain Models
-keep class com.dinepos.app.data.dto.** { *; }
-keep class com.dinepos.app.domain.model.** { *; }

# Security Crypto
-keep class androidx.security.crypto.** { *; }

# ZXing Core
-keep class com.google.zxing.** { *; }

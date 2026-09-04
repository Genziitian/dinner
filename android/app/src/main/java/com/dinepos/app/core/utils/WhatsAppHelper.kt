package com.dinepos.app.core.utils

import android.content.Context
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import java.net.URLEncoder

object WhatsAppHelper {

    /**
     * Directly opens the customer's chat in WhatsApp without loading a web browser first.
     * Uses the direct whatsapp://send URI and explicitly targets the WhatsApp or WhatsApp Business application package.
     */
    fun openChat(context: Context, phone: String, message: String) {
        val cleanDigits = phone.filter { it.isDigit() }.removePrefix("91").takeLast(10)
        if (cleanDigits.length != 10) {
            Toast.makeText(context, "Invalid phone number", Toast.LENGTH_SHORT).show()
            return
        }

        val fullPhone = "91$cleanDigits"
        val encodedMsg = try {
            URLEncoder.encode(message, "UTF-8")
        } catch (_: Exception) {
            message
        }

        // 1. Direct whatsapp:// URI
        val directUri = Uri.parse("whatsapp://send?phone=$fullPhone&text=$encodedMsg")
        val directIntent = Intent(Intent.ACTION_VIEW, directUri).apply {
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        }

        val pm = context.packageManager
        val isWhatsAppInstalled = try {
            pm.getPackageInfo("com.whatsapp", 0)
            true
        } catch (_: Exception) {
            false
        }
        val isBusinessInstalled = try {
            pm.getPackageInfo("com.whatsapp.w4b", 0)
            true
        } catch (_: Exception) {
            false
        }

        if (isWhatsAppInstalled) {
            directIntent.setPackage("com.whatsapp")
        } else if (isBusinessInstalled) {
            directIntent.setPackage("com.whatsapp.w4b")
        }

        try {
            context.startActivity(directIntent)
        } catch (_: Exception) {
            // 2. Fallback to https link if direct protocol fails
            try {
                val fallbackUri = Uri.parse("https://api.whatsapp.com/send?phone=$fullPhone&text=$encodedMsg")
                val fallbackIntent = Intent(Intent.ACTION_VIEW, fallbackUri).apply {
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
                context.startActivity(fallbackIntent)
            } catch (_: Exception) {
                Toast.makeText(context, "WhatsApp is not installed on this device", Toast.LENGTH_SHORT).show()
            }
        }
    }
}

package com.dinepos.app.core.utils

import android.content.ContentValues
import android.content.Context
import android.content.Intent
import android.media.MediaScannerConnection
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import android.util.Log
import androidx.core.content.FileProvider
import com.dinepos.app.data.dto.ExportDataResponseDto
import java.io.File
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class ExportDownloadResult(
    val success: Boolean,
    val filename: String,
    val contentUri: Uri? = null,
    val relativePath: String = "Downloads/DinePOS",
    val message: String = ""
)

object ExportDownloadHelper {

    private const val TAG = "ExportDownloadHelper"

    /**
     * Escape CSV values according to RFC 4180
     */
    fun escapeCsv(value: String?): String {
        if (value.isNullOrEmpty()) return ""
        val clean = value.replace("\r", " ").replace("\n", " ")
        return if (clean.contains(",") || clean.contains("\"")) {
            "\"" + clean.replace("\"", "\"\"") + "\""
        } else {
            clean
        }
    }

    /**
     * Convert ExportDataResponseDto into formatted CSV text
     */
    fun buildSalesCsv(data: ExportDataResponseDto, isMill: Boolean = false): String {
        val sb = StringBuilder()

        val headerTitle = if (isMill) "MILL SALES EXPORT REPORT" else "RESTAURANT SALES EXPORT REPORT"
        sb.append(escapeCsv(headerTitle)).append("\n")
        sb.append("Business Name,").append(escapeCsv(data.restaurantName)).append("\n")
        sb.append("Export Range,").append(escapeCsv("${data.startDate} to ${data.endDate}")).append("\n")
        sb.append("Total Orders,").append(data.stats?.totalOrders ?: data.orders.size).append("\n")
        sb.append("Total Revenue (INR),").append(String.format(Locale.US, "%.2f", data.stats?.totalSales ?: 0.0)).append("\n")
        sb.append("Cash Sales (INR),").append(String.format(Locale.US, "%.2f", data.stats?.cashSales ?: 0.0)).append("\n")
        sb.append("Online Sales (INR),").append(String.format(Locale.US, "%.2f", data.stats?.onlineSales ?: 0.0)).append("\n\n")

        // Table Header
        sb.append("Order #,Date,Time,Customer Name,Phone,Items Summary,Total Items,Subtotal (INR),Total (INR),Payment Method,Status,Billed By\n")

        if (data.orders.isEmpty()) {
            sb.append("No transactions recorded for this period.,,,,,,,,,,,\n")
        } else {
            for (order in data.orders) {
                val itemSummaries = mutableListOf<String>()
                var totalQty = 0.0
                for (item in order.items) {
                    val qty = item.quantity
                    totalQty += qty
                    val itemLabel = item.itemName.ifBlank { item.itemNameDirect ?: "Item" }
                    val variant = item.variantName.ifBlank { item.variantNameDirect ?: "" }
                    val label = if (variant.isNotBlank()) "$itemLabel ($variant)" else itemLabel
                    itemSummaries.add("$label x $qty ${item.unit} = Rs.${String.format(Locale.US, "%.2f", item.totalPrice)}")
                }
                val itemsSummaryText = itemSummaries.joinToString(" | ")

                val row = listOf(
                    order.orderNumber.toString(),
                    escapeCsv(order.orderDate),
                    escapeCsv(order.orderTime),
                    escapeCsv(order.customerName ?: "Walk-in Customer"),
                    escapeCsv(order.customerPhone ?: ""),
                    escapeCsv(itemsSummaryText),
                    String.format(Locale.US, "%.1f", totalQty),
                    String.format(Locale.US, "%.2f", order.subtotal),
                    String.format(Locale.US, "%.2f", order.total.takeIf { it > 0.0 } ?: (order.totalAmount ?: 0.0)),
                    escapeCsv(order.paymentMethod),
                    escapeCsv(order.status),
                    escapeCsv(order.createdByUsername ?: "Manager")
                )
                sb.append(row.joinToString(",")).append("\n")
            }
        }

        return sb.toString()
    }

    /**
     * Save text file (CSV or JSON) directly into public Downloads folder and app cache.
     */
    fun saveToDownloads(
        context: Context,
        filename: String,
        content: String,
        mimeType: String = "text/csv"
    ): ExportDownloadResult {
        return try {
            // 1. Write to internal cache for FileProvider sharing
            val cacheDir = File(context.cacheDir, "exports").apply { mkdirs() }
            val cacheFile = File(cacheDir, filename)
            cacheFile.outputStream().use { out ->
                if (mimeType == "text/csv") {
                    out.write(byteArrayOf(0xEF.toByte(), 0xBB.toByte(), 0xBF.toByte())) // UTF-8 BOM
                }
                out.write(content.toByteArray(Charsets.UTF_8))
            }

            val fileUri = FileProvider.getUriForFile(
                context,
                "${context.packageName}.fileprovider",
                cacheFile
            )

            // 2. Save directly to Android Public Downloads folder
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                val resolver = context.contentResolver
                val contentValues = ContentValues().apply {
                    put(MediaStore.Downloads.DISPLAY_NAME, filename)
                    put(MediaStore.Downloads.MIME_TYPE, mimeType)
                    put(MediaStore.Downloads.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS + "/DinePOS")
                    put(MediaStore.Downloads.IS_PENDING, 1)
                }

                val collection = MediaStore.Downloads.getContentUri(MediaStore.VOLUME_EXTERNAL_PRIMARY)
                val downloadUri = resolver.insert(collection, contentValues)
                if (downloadUri != null) {
                    resolver.openOutputStream(downloadUri)?.use { out ->
                        if (mimeType == "text/csv") {
                            out.write(byteArrayOf(0xEF.toByte(), 0xBB.toByte(), 0xBF.toByte()))
                        }
                        out.write(content.toByteArray(Charsets.UTF_8))
                    }
                    contentValues.clear()
                    contentValues.put(MediaStore.Downloads.IS_PENDING, 0)
                    resolver.update(downloadUri, contentValues, null, null)
                }
            } else {
                val downloadsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)
                val dinePosDir = File(downloadsDir, "DinePOS").apply { mkdirs() }
                val publicFile = File(dinePosDir, filename)
                publicFile.outputStream().use { out ->
                    if (mimeType == "text/csv") {
                        out.write(byteArrayOf(0xEF.toByte(), 0xBB.toByte(), 0xBF.toByte()))
                    }
                    out.write(content.toByteArray(Charsets.UTF_8))
                }
                MediaScannerConnection.scanFile(
                    context,
                    arrayOf(publicFile.absolutePath),
                    arrayOf(mimeType),
                    null
                )
            }

            ExportDownloadResult(
                success = true,
                filename = filename,
                contentUri = fileUri,
                relativePath = "Downloads/DinePOS",
                message = "File saved to Downloads/DinePOS/$filename"
            )
        } catch (e: Exception) {
            Log.e(TAG, "Error saving export to Downloads", e)
            ExportDownloadResult(
                success = false,
                filename = filename,
                message = e.localizedMessage ?: "Failed to save export file"
            )
        }
    }

    /**
     * Open downloaded file in user's installed spreadsheet or text viewer (e.g. Google Sheets, Excel).
     */
    fun openFile(context: Context, contentUri: Uri, mimeType: String = "text/csv") {
        try {
            val intent = Intent(Intent.ACTION_VIEW).apply {
                setDataAndType(contentUri, mimeType)
                addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            val chooser = Intent.createChooser(intent, "Open file with").apply {
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            context.startActivity(chooser)
        } catch (e: Exception) {
            Log.e(TAG, "Cannot open file intent", e)
        }
    }

    /**
     * Share downloaded file via WhatsApp, Gmail, Drive, etc.
     */
    fun shareFile(context: Context, contentUri: Uri, subject: String, mimeType: String = "text/csv") {
        try {
            val intent = Intent(Intent.ACTION_SEND).apply {
                type = mimeType
                putExtra(Intent.EXTRA_STREAM, contentUri)
                putExtra(Intent.EXTRA_SUBJECT, subject)
                addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            val chooser = Intent.createChooser(intent, "Share file").apply {
                addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            }
            context.startActivity(chooser)
        } catch (e: Exception) {
            Log.e(TAG, "Cannot share file intent", e)
        }
    }

    /**
     * Generate safe filename with timestamp
     */
    fun generateFilename(businessName: String, type: String, rangeLabel: String, extension: String = "csv"): String {
        val sanitizedName = businessName.replace(Regex("[^a-zA-Z0-9_]"), "_").trim('_').ifBlank { "DinePOS" }
        val dateStamp = SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(Date())
        return "${sanitizedName}_${type}_${rangeLabel}_${dateStamp}.${extension}"
    }
}

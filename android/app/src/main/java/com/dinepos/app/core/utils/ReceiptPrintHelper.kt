package com.dinepos.app.core.utils

import android.content.Context
import android.print.PrintAttributes
import android.print.PrintManager
import android.webkit.WebView
import android.webkit.WebViewClient
import com.dinepos.app.domain.model.Order

object ReceiptPrintHelper {

    fun printOrDownloadPdf(
        context: Context,
        order: Order,
        restaurantName: String,
        restaurantAddress: String = "",
        restaurantPhone: String = "",
        billerName: String = ""
    ) {
        val webView = WebView(context)
        val htmlContent = generateReceiptHtml(
            order = order,
            restaurantName = restaurantName,
            restaurantAddress = restaurantAddress,
            restaurantPhone = restaurantPhone,
            billerName = billerName
        )

        webView.webViewClient = object : WebViewClient() {
            override fun onPageFinished(view: WebView?, url: String?) {
                val printManager = context.getSystemService(Context.PRINT_SERVICE) as? PrintManager
                val printAdapter = webView.createPrintDocumentAdapter("Receipt_${order.orderNumber}")
                val attributes = PrintAttributes.Builder()
                    .setMediaSize(PrintAttributes.MediaSize.ISO_A4)
                    .setResolution(PrintAttributes.Resolution("res1", "default", 300, 300))
                    .setMinMargins(PrintAttributes.Margins.NO_MARGINS)
                    .build()

                printManager?.print("DinePOS_Receipt_${order.orderNumber}", printAdapter, attributes)
            }
        }

        webView.loadDataWithBaseURL(null, htmlContent, "text/html", "UTF-8", null)
    }

    private fun generateReceiptHtml(
        order: Order,
        restaurantName: String,
        restaurantAddress: String,
        restaurantPhone: String,
        billerName: String
    ): String {
        val itemsHtml = order.items.joinToString("") { item ->
            """
            <tr>
                <td style="padding: 6px 0; border-bottom: 1px dashed #e2e8f0;">
                    <div style="font-weight: bold; color: #0f172a;">${item.itemName}</div>
                    <div style="font-size: 12px; color: #64748b;">${item.variantName} × ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)}</div>
                </td>
                <td style="padding: 6px 0; text-align: right; font-weight: bold; border-bottom: 1px dashed #e2e8f0; color: #0f172a;">
                    ${CurrencyFormatter.formatInr(item.totalPrice)}
                </td>
            </tr>
            """.trimIndent()
        }

        return """
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                    padding: 24px;
                    color: #0f172a;
                    max-width: 480px;
                    margin: 0 auto;
                }
                .header { text-align: center; margin-bottom: 18px; }
                .restaurant-name { font-size: 22px; font-weight: 800; color: #ea580c; }
                .restaurant-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
                .order-title { font-size: 18px; font-weight: 700; margin-top: 8px; }
                .meta { font-size: 12px; color: #64748b; margin-top: 4px; }
                .biller-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; margin-top: 8px; text-align: center; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 16px; }
                .totals { margin-top: 16px; border-top: 2px solid #0f172a; padding-top: 12px; }
                .total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; }
                .footer { text-align: center; font-size: 11px; color: #94a3b8; margin-top: 24px; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="restaurant-name">$restaurantName</div>
                ${if (restaurantAddress.isNotBlank()) "<div class=\"restaurant-meta\">$restaurantAddress</div>" else ""}
                ${if (restaurantPhone.isNotBlank()) "<div class=\"restaurant-meta\">Tel: $restaurantPhone</div>" else ""}
                
                <div class="order-title">RECEIPT #ORDER-${order.orderNumber}</div>
                <div class="meta">${order.orderDate} · ${order.orderTime}</div>
                
                <div class="biller-box" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; margin-top: 10px; font-size: 12px;">
                    <div style="text-align: left;">
                        <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Customer</div>
                        <div style="font-weight: bold; color: #0f172a;">${if (!order.customerName.isNullOrBlank()) order.customerName else "Walk-in Customer"}</div>
                        ${if (!order.customerPhone.isNullOrBlank()) "<div style=\"color: #64748b; font-size: 11px;\">${order.customerPhone}</div>" else ""}
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Billed by</div>
                        <div style="font-weight: bold; color: #0f172a;">${if (billerName.isNotBlank()) billerName else "Cashier"}</div>
                    </div>
                </div>
            </div>

            <table>
                <tbody>
                    $itemsHtml
                </tbody>
            </table>

            <div class="totals">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; margin-bottom: 6px;">
                    <span>Payment Method</span>
                    <span><b>${order.paymentMethod}</b></span>
                </div>
                <div class="total-row">
                    <span>TOTAL AMOUNT</span>
                    <span style="color: #ea580c;">${CurrencyFormatter.formatInr(order.total)}</span>
                </div>
            </div>

            <div class="footer">
                <p style="font-weight: bold; color: #64748b; margin-bottom: 4px;">Scan to view E-receipt</p>
                <p>Thank you for dining with us!</p>
                <p>Powered by DinePOS</p>
            </div>
        </body>
        </html>
        """.trimIndent()
    }
}

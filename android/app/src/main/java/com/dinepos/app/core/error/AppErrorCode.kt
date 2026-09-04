package com.dinepos.app.core.error

/**
 * Standardized error codes across DinePOS.
 * Used for technical debugging, user-facing error reporting, and localized error messages.
 */
enum class AppErrorCode(
    val code: String,
    val httpStatus: Int?,
    val titleEn: String,
    val titleHi: String,
    val descriptionEn: String,
    val descriptionHi: String,
    val actionEn: String,
    val actionHi: String
) {
    // -------------------------------------------------------------
    // Network & Connectivity Errors (NET_000s)
    // -------------------------------------------------------------
    ERR_NO_INTERNET(
        code = "ERR_NET_001",
        httpStatus = null,
        titleEn = "No Internet Connection",
        titleHi = "इंटरनेट कनेक्शन नहीं है",
        descriptionEn = "Your device is currently offline. Please check your Wi-Fi or mobile data connection and try again.",
        descriptionHi = "आपका डिवाइस इस समय ऑफ़लाइन है। कृपया अपना वाई-फ़ाई या मोबाइल डेटा जांचें और पुनः प्रयास करें।",
        actionEn = "Check Network & Retry",
        actionHi = "नेटवर्क जांचें और पुनः प्रयास करें"
    ),

    ERR_NETWORK_TIMEOUT(
        code = "ERR_NET_002",
        httpStatus = null,
        titleEn = "Connection Timed Out",
        titleHi = "कनेक्शन का समय समाप्त हुआ",
        descriptionEn = "The server took too long to respond. The network might be slow or unstable.",
        descriptionHi = "सर्वर से उत्तर आने में बहुत अधिक समय लगा। नेटवर्क धीमा या अस्थिर हो सकता है।",
        actionEn = "Try Again",
        actionHi = "पुनः प्रयास करें"
    ),

    ERR_SERVER_UNREACHABLE(
        code = "ERR_NET_003",
        httpStatus = null,
        titleEn = "Server Unreachable",
        titleHi = "सर्वर से संपर्क नहीं हो पाया",
        descriptionEn = "Unable to connect to the backend server. The server may be undergoing maintenance or the URL is unreachable.",
        descriptionHi = "बैकएंड सर्वर से कनेक्ट नहीं हो सका। सर्वर मेंटेनेंस पर हो सकता है या नेटवर्क समस्या है।",
        actionEn = "Retry Connection",
        actionHi = "कनेक्शन पुनः प्रयास करें"
    ),

    // -------------------------------------------------------------
    // HTTP & API Status Errors (HTTP_000s)
    // -------------------------------------------------------------
    ERR_HTTP_BAD_REQUEST(
        code = "ERR_REQ_400",
        httpStatus = 400,
        titleEn = "Invalid Request",
        titleHi = "अमान्य अनुरोध",
        descriptionEn = "The request could not be processed due to invalid parameters or incomplete form data.",
        descriptionHi = "गलत इनपुट या अधूरी जानकारी के कारण अनुरोध स्वीकार नहीं किया जा सका।",
        actionEn = "Check Details",
        actionHi = "विवरण जांचें"
    ),

    ERR_HTTP_UNAUTHORIZED(
        code = "ERR_AUTH_401",
        httpStatus = 401,
        titleEn = "Session Expired",
        titleHi = "सत्र समाप्त हो गया",
        descriptionEn = "Your login session has expired or security credentials are invalid. Please sign in again.",
        descriptionHi = "आपका लॉगिन सत्र समाप्त हो गया है। कृपया पुनः साइन इन करें।",
        actionEn = "Sign In Again",
        actionHi = "पुनः साइन इन करें"
    ),

    ERR_HTTP_FORBIDDEN(
        code = "ERR_AUTH_403",
        httpStatus = 403,
        titleEn = "Access Denied",
        titleHi = "पहुंच अस्वीकृत",
        descriptionEn = "You do not have permission to view this resource or perform this action.",
        descriptionHi = "आपके पास इस जानकारी को देखने या इस कार्य को करने की अनुमति नहीं है।",
        actionEn = "Go to Dashboard",
        actionHi = "डैशबोर्ड पर जाएं"
    ),

    ERR_HTTP_NOT_FOUND(
        code = "ERR_HTTP_404",
        httpStatus = 404,
        titleEn = "Page or Record Not Found",
        titleHi = "पेज या रिकॉर्ड नहीं मिला (404)",
        descriptionEn = "The requested screen, customer record, or order could not be found or has been removed.",
        descriptionHi = "अनुरोधित पेज, ग्राहक खाता या ऑर्डर नहीं मिला या हटाया जा चुका है।",
        actionEn = "Back to Dashboard",
        actionHi = "डैशबोर्ड पर लौटें"
    ),

    ERR_HTTP_SERVER_ERROR(
        code = "ERR_SRV_500",
        httpStatus = 500,
        titleEn = "Internal Server Error",
        titleHi = "सर्वर आंतरिक त्रुटि (500)",
        descriptionEn = "The server encountered an unexpected error while processing your request.",
        descriptionHi = "अनुरोध प्रोसेस करते समय सर्वर में अनपेक्षित त्रुटि हुई। कृपया थोड़ी देर बाद पुनः प्रयास करें।",
        actionEn = "Retry Later",
        actionHi = "बाद में पुनः प्रयास करें"
    ),

    ERR_HTTP_SERVICE_UNAVAILABLE(
        code = "ERR_SRV_503",
        httpStatus = 503,
        titleEn = "Service Temporarily Unavailable",
        titleHi = "सेवा अस्थायी रूप से अनुपलब्ध",
        descriptionEn = "The server is currently unable to handle the request due to maintenance or overload.",
        descriptionHi = "सर्वर वर्तमान में रखरखाव या अधिक लोड के कारण अनुरोध पूरा नहीं कर सकता।",
        actionEn = "Refresh Page",
        actionHi = "पेज रिफ्रेश करें"
    ),

    // -------------------------------------------------------------
    // Data & Parsing Errors (DATA_000s)
    // -------------------------------------------------------------
    ERR_DATA_PARSING(
        code = "ERR_DATA_001",
        httpStatus = null,
        titleEn = "Data Format Error",
        titleHi = "डेटा प्रारूप त्रुटि",
        descriptionEn = "Failed to parse the response received from the server. The data format may be invalid.",
        descriptionHi = "सर्वर से प्राप्त डेटा को पढ़ने में त्रुटि हुई। डेटा का प्रारूप अमान्य हो सकता है।",
        actionEn = "Report Issue",
        actionHi = "समस्या रिपोर्ट करें"
    ),

    // -------------------------------------------------------------
    // Generic / Fallback Error (SYS_999)
    // -------------------------------------------------------------
    ERR_UNKNOWN(
        code = "ERR_SYS_999",
        httpStatus = null,
        titleEn = "Something Went Wrong",
        titleHi = "कुछ गड़बड़ हो गई",
        descriptionEn = "An unexpected error occurred. Please try again or contact system support.",
        descriptionHi = "एक अनपेक्षित त्रुटि हुई। कृपया पुनः प्रयास करें या सहायता से संपर्क करें।",
        actionEn = "Try Again",
        actionHi = "पुनः प्रयास करें"
    );

    fun title(isHi: Boolean): String = if (isHi) titleHi else titleEn
    fun description(isHi: Boolean): String = if (isHi) descriptionHi else descriptionEn
    fun action(isHi: Boolean): String = if (isHi) actionHi else actionEn

    companion object {
        fun fromHttpStatus(status: Int): AppErrorCode {
            return when (status) {
                400 -> ERR_HTTP_BAD_REQUEST
                401 -> ERR_HTTP_UNAUTHORIZED
                403 -> ERR_HTTP_FORBIDDEN
                404 -> ERR_HTTP_NOT_FOUND
                500 -> ERR_HTTP_SERVER_ERROR
                502, 503, 504 -> ERR_HTTP_SERVICE_UNAVAILABLE
                else -> if (status in 400..499) ERR_HTTP_BAD_REQUEST else ERR_HTTP_SERVER_ERROR
            }
        }

        fun fromCode(code: String?): AppErrorCode {
            if (code.isNullOrBlank()) return ERR_UNKNOWN
            return values().find { it.code.equals(code, ignoreCase = true) || it.name.equals(code, ignoreCase = true) }
                ?: ERR_UNKNOWN
        }
    }
}

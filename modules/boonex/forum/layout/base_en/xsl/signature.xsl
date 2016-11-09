<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

    <xsl:template name="signature">
        <xsl:param name="text" />

                    <div class="forum_default_margin_top">
                        <a href="javascript:void(0);" onclick="jQuery('#forum_signature').toggle(f._speed);">Change Signature</a>
                        <div id="forum_signature" style="display:none;">
                            <div class="forum_field_error_message" style="display:none" id="err_signature">Maximum is 100 characters</div>
                            <input type="text" name="signature" style="width:70%;" value="{$text}" maxlength="100" />
                        </div>
                    </div>

    </xsl:template>

</xsl:stylesheet>

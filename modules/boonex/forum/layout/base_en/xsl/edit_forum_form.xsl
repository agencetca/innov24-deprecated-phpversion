<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:template match="urls" />

<xsl:template match="forum">

	<div class="wnd_box">
	    <div style="display:none;" id="js_code">
            var f = document.forms['orca_edit_forum'];						
            orca_admin.editForumSubmit (
                f.elements['cat_id'].value, 
                '<xsl:value-of select="cat_uri" />',
                f.elements['forum_id'].value,
                f.elements['forum_title'].value,
                f.elements['forum_desc'].value,
                f.elements['forum_type'].value,
				f.elements['forum_order'].value
            );
		</div>			

        <div class="wnd_title">
            <h2>
                <xsl:if test="@forum_id &gt; 0">Edit forum</xsl:if>
                <xsl:if test="0 = @forum_id">New forum</xsl:if>
            </h2>
        </div>			

		<div class="wnd_content">
            <form name="orca_edit_forum" onsubmit="var x=document.getElementById('js_code').innerHTML; eval(x); return false;">

                <fieldset class="form_field_row"><legend>Forum title:</legend>
                    <input class="sh" type="text" name="forum_title" value="{title}" /> 
                </fieldset>
                <br /><br />

                <fieldset class="form_field_row"><legend>Forum description:</legend>
                    <input class="sh" type="text" name="forum_desc" value="{desc}" /> 
                </fieldset>
                <br /><br />

                <fieldset class="form_field_row"><legend>Forum order:</legend>
                    <input class="sh" type="text" name="forum_order" value="{order}" /> 
                </fieldset>
                <br /><br />

                <fieldset class="form_field_row"><legend>Forum type:</legend>
                    <select name="forum_type">
                        <xsl:element name="option">
                            <xsl:attribute name="value">public</xsl:attribute>
                            <xsl:if test="'public' = type">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            public
                        </xsl:element>
                        <xsl:element name="option">
                            <xsl:attribute name="value">private</xsl:attribute>
                            <xsl:if test="'private' = type"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            private
                        </xsl:element>
                    </select>
                </fieldset>

				<input type="hidden" name="forum_id" value="{@forum_id}" />
				<input type="hidden" name="cat_id" value="{cat_id}" />
				<input type="hidden" name="action" value="edit_forum_submit" />

				<div class="forum_default_padding">
                    <input type="submit" name="submit_form" value="Submit" onclick="var x=document.getElementById('js_code').innerHTML; eval(x); return false;" />
                    <input type="reset" value="Cancel" onclick="f.hideHTML(); return false;" class="forum_default_margin_left" />
                </div>

			</form>
        </div>

	</div>

</xsl:template>

</xsl:stylesheet>



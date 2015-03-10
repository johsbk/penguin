{%- macro input (dict) -%}
<input class="{{ dict.class }}" type="{{ dict.type|default('text') }}" id="{{ dict.id|default(dict.name) }}" name="{{ dict.name }}" value="{{ dict.value|e }}" />
{%- endmacro -%}
{%- macro number (dict) -%}
	{% set dict = dict|merge({'class':'a-right'}) %}
	{{ _self.input(dict) }}
{%- endmacro -%}
{%- macro hidden (dict) -%}
	{% set dict = dict|merge({'type':'hidden'}) %}
	{{ _self.input(dict) }}
{%- endmacro -%}
{%- macro checkbox (dict) -%}
	{{- _self.hidden(dict) -}}<input type="checkbox" onclick="javascript:this.previousSibling.value=(this.previousSibling.value=='true'?'false':'true')"
	{%- if dict.value|default('false')=='true' -%}
		checked="checked"
	{%- endif -%}
	 />
{%- endmacro -%}
{%- macro text (dict) -%}
	<textarea class="{{ dict.class }}" id="{{ dict.id|default(dict.name) }}" name="{{ dict.name }}">{{ dict.value|e }}</textarea>
{%- endmacro -%}
{%- macro richtext (dict) -%}
	{%- set dict = dict|merge({'class':'richtext'}) -%}
	{{ _self.text(dict) }}
{%- endmacro -%}
{%- macro select (dict) -%}
	<select id="{{ dict.id|default(dict.name) }}" name="{{ dict.name }}"
	{%- if dict.onchange %}
		onchange="{{ dict.onchange|raw }}"
	{%- endif -%}>
	{% if dict.first %}
		<option value="{{ dict.first.value|default(0) }}">{{ dict.first.option|default('None') }}</option>
	{% endif %}
	
	{% for obj in dict.objects %}
		<option value="{{ obj|get(dict.value|default('id')) }}" 
		{%- if obj|get(dict.value|default('id'))==dict.default|default(0) -%}
			selected="selected"
		{%- endif -%}>{{ obj|get(dict.option|default('name')) }}</option>
	{% endfor %}
	</select>
{% endmacro %}
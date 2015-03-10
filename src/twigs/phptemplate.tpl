{% extends ajax ? "ajax.tpl" : "layout.tpl" %}
{% block content %}
	{{ include_php(path) }}
	
{% endblock %}
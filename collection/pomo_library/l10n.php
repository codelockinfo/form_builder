<?php
function get_locale() {
        return $locale = 'en_US';
}
function translate( $text, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate( $text );
        return $translation;
}
function __( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}
function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( translate( $text, $domain ) );
}
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( translate( $text, $domain ) );
}
function _e( $text, $domain = 'default' ) {
	echo translate( $text, $domain );
}
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html( translate( $text, $domain ) );
}

function _x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	return  $translations->translate( $text, $context );	 
}
function load_textdomain( $domain, $mofile ) {
	global $l10n, $l10n_unloaded;
	$l10n_unloaded = (array) $l10n_unloaded;
	if ( !is_readable( $mofile ) ) return false;
	$mo = new MO();
	if ( !$mo->import_from_file( $mofile ) ) return false;
	if ( isset( $l10n[$domain] ) )
		$mo->merge_with( $l10n[$domain] );

	unset( $l10n_unloaded[ $domain ] );
	$l10n[$domain] = &$mo;
	return true;
}
function _load_textdomain_just_in_time( $domain ) {
	global $l10n_unloaded;
	$l10n_unloaded = (array) $l10n_unloaded;
	if ( 'default' === $domain || isset( $l10n_unloaded[ $domain ] ) ) {
		return false;
	}
	$translation_path = _get_path_to_translation( $domain );
	if ( false === $translation_path ) {
		return false;
	}
	return load_textdomain( $domain, $translation_path );
}
function get_translations_for_domain( $domain ) {
	global $l10n;
	if ( isset( $l10n[ $domain ] ) || ( _load_textdomain_just_in_time( $domain ) && isset( $l10n[ $domain ] ) ) ) {
		return $l10n[ $domain ];
	}
	static $noop_translations = null;
	if ( null === $noop_translations ) {
		$noop_translations = new NOOP_Translations;
	}
	return $noop_translations;
}
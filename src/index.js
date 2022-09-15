import { TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

const NavigationSectionTitlePlugin = () => {
	const { meta } = useSelect( ( select ) => {
		return {
			meta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
		};
	} );

	const { editPost } = useDispatch( 'core/editor', [
		meta._navigation_section_title,
	] );

	return (
		<PluginDocumentSettingPanel
			name="navigation-section-title"
			title={ __( 'Navigation Title' ) }
			icon={ <></> }
		>
			<TextControl
				value={ meta._navigation_section_title }
				onChange={ ( value ) => {
					editPost( {
						meta: { _navigation_section_title: value },
					} );
				} }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'navigation-section-title', {
	render: NavigationSectionTitlePlugin,
} );

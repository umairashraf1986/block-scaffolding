// Add the JS code to this file. On running npm run dev, it will compile to js/dist/.
import ServerSideRender from '@wordpress/server-side-render';
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType( 'scaffolding/scaffolding-block', {
	title: __('AMP Validation Statistics', 'block-scaffolding'),

	description: __('AMP validation statistics and template mode', 'block-scaffolding'),

    icon: 'performance',

    category: 'layout',

	edit: (props) => {

    const { attributes: {isTemplateModeVisible}, attributes, setAttributes } = props;

    return (
	<>
		<InspectorControls>
			<PanelBody title={ __('Additional Statistic', 'block-scaffolding') } >
				<ToggleControl
					label={ __("Display AMP template mode", "block-scaffolding") }
					checked={ isTemplateModeVisible }
					onChange={ () => setAttributes({ isTemplateModeVisible: !isTemplateModeVisible }) }
				/>
			</PanelBody>
		</InspectorControls>
		<ServerSideRender
			block="scaffolding/scaffolding-block"
			attributes={ attributes }
	    />
	</>
	);

    },

    save: () => {
 		return null;
    }
});

export function add( to, howMuch ) {
	return to + howMuch;
}

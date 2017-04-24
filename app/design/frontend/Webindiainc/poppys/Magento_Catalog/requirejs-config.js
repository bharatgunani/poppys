/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		"*": {
 			vesallOwlCarousel: "Magento_Catalog/owl.carousel.min"
 			
 		}
 	},
 	shim: {
        'Magento_Catalog/js/bootstrap.min': {
            'deps': ['jquery']
        }
    }
 };
# SliderHome
Module for show a slider of products on homepage

NOTE: Open admin magento and go to menu CONTENT -> Pages and edit the homepage, add the next code on Design - Layout Update XML

<referenceContainer name="content">
  <block class="Magmalabs\Slider\Block\Index\Index" name="slider_index_index"   template="Magmalabs_Slider::slider_index_index.phtml"/>
</referenceContainer>

<head>
  <css src="Magmalabs_Slider::css/slider.css"/>
</head>

save it and now we go to menu STORES -> Configuration -> Magmalabs -> Slider Home 
and configurate it how you want.


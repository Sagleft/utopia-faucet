echo "Downloading UIkit 3.2.6.."
echo "collecting css..."
python3 dfile.py https://cdn.jsdelivr.net/npm/uikit@3.2.6/dist/css/uikit.min.css ../controller/public_html/css/external/uikit.min.css
echo "collecting js..."
python3 dfile.py https://cdn.jsdelivr.net/npm/uikit@3.2.6/dist/js/uikit.min.js ../controller/public_html/js/external/uikit.min.js
python3 dfile.py https://cdn.jsdelivr.net/npm/uikit@3.2.6/dist/js/uikit-icons.min.js ../controller/public_html/js/external/uikit-icons.min.js
echo "Downloading Jquery 2.0.3.."
python3 dfile.py https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js ../controller/public_html/js/external/jquery.min.js

# echo "Downloading ionicons 3.0.0.."
# curl 'https://cdnjs.cloudflare.com/ajax/libs/ionicons/3.0.0/css/ionicons.css' > ../controller/public_html/css/external/ionicons.css

#echo "Downloading OwlCarousel 2.3.4.."
#python dfile.py https://github.com/OwlCarousel2/OwlCarousel2/archive/2.3.4.tar.gz owl.tar.gz
#tar -xzf owl.tar.gz OwlCarousel2-2.3.4/dist/owl.carousel.min.js --strip=2
#mv owl.carousel.min.js ../controller/public_html/js/external
#rm -f owl.tar.gz

echo "Done"

#!/bin/bash

for i in $( ls | grep Stat_ ); do
	echo "Starting $i conversion"
	# mogrify -background white -flatten export/$i/*.png
	mogrify -flatten -background white -format jpg export/$i/*.png
	cp $i/poster.jpg export/$i/image001.jpg
	rm export/$i/animation.*
	# ffmpeg -y -f image2 -framerate 40 -pattern_type glob -i "export/$i/*.png" export/$i/image%03d.png -c:v libx264 -pix_fmt yuv420p export/$i/animation.mp4
	ffmpeg -y -f image2 -framerate 40 -pattern_type glob -i "export/$i/*.jpg" export/$i/image%03d.jpg -c:v libx264 -pix_fmt yuv420p export/$i/animation.mp4
	ffmpeg -i export/$i/animation.mp4 -c:v libtheora -c:a libvorbis export/$i/animation.ogv
	ffmpeg -i export/$i/animation.mp4 -c:v libvpx -crf 10 -b:v 1M -c:a libvorbis export/$i/animation.webm

	rm ../../PGI/video/full/$i/fallback.jpg
	cp $i/fallback.jpg ../../PGI/video/full/$i/fallback.jpg

#	convert -loop 1 -delay 1/40 export/$i/*.png export/$i/animation.gif
	# Copy all of these things
	find "export/$i" -name 'animation.*' | while IFS= read -r foo; do 
	   file_name=$(basename $foo)
	   cp "$foo" "../../PGI/video/full/$i/$file_name"
	done 
	echo "Done converting $i"
done

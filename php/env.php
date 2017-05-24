<?php
# configure here the environment variables needed to run faust and all the scripts required by the online compiler

putenv("PATH=/opt/android/ndk:/opt/android/sdk/platform-tools:/opt/android/sdk/tools:/opt/local/libexec/qt5/bin:/opt/local/bin:/opt/local/sbin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin");

putenv("ANDROID_ROOT=/opt/android");
putenv("ANDROID_SDK_ROOT=/opt/android/sdk");
putenv("ANDROID_NDK_ROOT=/opt/android/ndk");
putenv("ANDROID_HOME=/opt/android/sdk");
putenv("ANDROID_NDK_HOME=/opt/android/ndk");
putenv("CPATH=/opt/ros/jade/include");

?>

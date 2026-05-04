<?php
function rrt_get_public_post_category()
{
    return "route category";
}
function rrt_get_public_post_detail()
{
}
function rrt_get_route_studio()
{
    return rrt_route('public/studio/home/index');
}
function rrt_get_route_admin()
{
    return rrt_route('admin/dashboard/index');
}

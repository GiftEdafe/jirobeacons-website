export async function onRequestPost(context) {
  try {
    const input = await context.request.json();
    const { name, email, department, message } = input;

    if (!name || !email || !message) {
      return new Response(JSON.stringify({ error: "Missing required fields" }), {
        status: 400,
        headers: { "Content-Type": "application/json", "Access-Control-Allow-Origin": "*" },
      });
    }

    const resendResponse = await fetch("https://api.resend.com/emails", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${context.env.RESEND_API_KEY}`,
      },
      body: JSON.stringify({
        from: "Jiro-BEACONS Website <onboarding@resend.dev>",
        to: ["jirobeaconseng@gmail.com"],
        reply_to: email,
        subject: `New Website Inquiry from ${name}`,
        text: `You have received a new message from your website contact form.\n\nName: ${name}\nEmail: ${email}\nDepartment: ${department || "N/A"}\n\nMessage:\n${message}`,
      }),
    });

    const data = await resendResponse.json();

    if (!resendResponse.ok) {
      throw new Error(data.message || "Failed to send email via Resend");
    }

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { "Content-Type": "application/json", "Access-Control-Allow-Origin": "*" },
    });

  } catch (err) {
    return new Response(JSON.stringify({ error: err.message }), {
      status: 500,
      headers: { "Content-Type": "application/json", "Access-Control-Allow-Origin": "*" },
    });
  }
}
